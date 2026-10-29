<?php

namespace App\Http\Controllers;

use App\Models\Pasokan;
use App\Models\Pemasok;
use App\Models\Produk;
use App\Models\BatchBeras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PasokanController extends Controller
{
    public function index()
    {
        $pasokans = Pasokan::with(['pemasok', 'produk', 'user'])
            ->orderBy('tgl_pasok', 'desc')
            ->paginate(20);
        return view('admin.pasokan.index', compact('pasokans'));
    }

    public function create()
    {
        $pemasoks = Pemasok::where('status_pemasok', 'aktif')->get();
        $produks  = Produk::orderBy('nama_produk')->get();
        return view('admin.pasokan.create', compact('pemasoks', 'produks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pemasok'      => 'required|integer',
            'id_produk'       => 'required|integer',
            'tgl_pasok'       => 'required|date',
            'tgl_panen_giling'=> 'required|date|before_or_equal:tgl_pasok',
            'tgl_kadaluarsa'  => 'required|date|after:tgl_pasok',
            'jumlah_kg'       => 'required|numeric|min:1',
            'harga_per_kg'    => 'required|numeric|min:0',
        ]);

        $user = Auth::guard('web')->user();
        $totalBayar = $data['jumlah_kg'] * $data['harga_per_kg'];

        DB::beginTransaction();
        try {
            // Buat batch FIFO baru
            $kodeBatch = 'BCH-' . strtoupper(substr(str_replace('-', '', $data['tgl_pasok']), 2)) . '-' . $data['id_produk'];
            $batch = BatchBeras::create([
                'id_produk'         => $data['id_produk'],
                'kode_batch'        => $kodeBatch . '-' . time(),
                'tgl_masuk'         => $data['tgl_pasok'],
                'tgl_panen_giling'  => $data['tgl_panen_giling'],
                'tgl_kadaluarsa'    => $data['tgl_kadaluarsa'],
                'stok_awal'         => $data['jumlah_kg'],
                'stok_sisa'         => $data['jumlah_kg'],
                'harga_pokok_beli'  => $data['harga_per_kg'],
                'status_batch'      => 'tersedia',
            ]);

            // Catat pasokan
            Pasokan::create([
                'id_pemasok'                => $data['id_pemasok'],
                'id_produk'                 => $data['id_produk'],
                'id_batch'                  => $batch->id_batch,
                'tgl_pasok'                 => $data['tgl_pasok'],
                'jumlah_kg'                 => $data['jumlah_kg'],
                'harga_per_kg'              => $data['harga_per_kg'],
                'total_bayar'               => $totalBayar,
                'status_pembayaran_petani'  => 'belum_lunas',
                'id_user'                   => $user->id_user,
            ]);

            // Update stok produk
            $produk = Produk::find($data['id_produk']);
            $produk->stok += $data['jumlah_kg'];
            $produk->save();

            DB::commit();
            return redirect()->route('admin.pasokan.index')->with('success', 'Data pasokan & batch FIFO baru berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pasokan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pasokan = Pasokan::findOrFail($id);
        $pasokan->delete();
        return redirect()->route('admin.pasokan.index')->with('success', 'Data pasokan berhasil dihapus.');
    }
}

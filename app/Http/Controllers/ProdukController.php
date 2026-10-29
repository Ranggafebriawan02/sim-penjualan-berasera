<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Kualitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::with(['kategori', 'kualitas'])
            ->orderBy('nama_produk')
            ->paginate(15);

        $stokMenipis = Produk::where('stok', '<', 50)->where('stok', '>', 0)->count();

        return view('admin.produk.index', compact('produk', 'stokMenipis'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $kualitas = Kualitas::all();
        return view('admin.produk.create', compact('kategoris', 'kualitas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kategori'  => 'required|integer',
            'id_kualitas'  => 'required|integer',
            'nama_produk'  => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
            'satuan'       => 'required|string|max:20',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|numeric|min:0',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = 'produk_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $data['gambar'] = $filename;
        }

        Produk::create($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk beras berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $produk    = Produk::findOrFail($id);
        $kategoris = Kategori::all();
        $kualitas  = Kualitas::all();
        return view('admin.produk.edit', compact('produk', 'kategoris', 'kualitas'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $data = $request->validate([
            'id_kategori'  => 'required|integer',
            'id_kualitas'  => 'required|integer',
            'nama_produk'  => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
            'satuan'       => 'required|string|max:20',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|numeric|min:0',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = 'produk_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $data['gambar'] = $filename;
        } else {
            unset($data['gambar']);
        }

        $produk->update($data);

        return redirect()->route('admin.produk.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}

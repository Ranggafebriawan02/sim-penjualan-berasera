<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasoks = Pemasok::withCount('pasokans')->orderBy('nama_pemasok')->paginate(15);
        return view('admin.pemasok.index', compact('pemasoks'));
    }

    public function create()
    {
        return view('admin.pemasok.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pemasok'   => 'required|string|max:100',
            'telepon'        => 'nullable|string|max:25',
            'alamat'         => 'nullable|string',
            'rekening_bank'  => 'nullable|string|max:100',
            'status_pemasok' => 'required|in:aktif,nonaktif',
        ]);

        Pemasok::create($data);
        return redirect()->route('admin.pemasok.index')->with('success', 'Data pemasok/petani berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pemasok = Pemasok::findOrFail($id);
        return view('admin.pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, $id)
    {
        $pemasok = Pemasok::findOrFail($id);

        $data = $request->validate([
            'nama_pemasok'   => 'required|string|max:100',
            'telepon'        => 'nullable|string|max:25',
            'alamat'         => 'nullable|string',
            'rekening_bank'  => 'nullable|string|max:100',
            'status_pemasok' => 'required|in:aktif,nonaktif',
        ]);

        $pemasok->update($data);
        return redirect()->route('admin.pemasok.index')->with('success', 'Data pemasok berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pemasok = Pemasok::findOrFail($id);
        $pemasok->delete();
        return redirect()->route('admin.pemasok.index')->with('success', 'Pemasok berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Kualitas;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'kualitas']);

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->filled('kualitas')) {
            $query->where('id_kualitas', $request->kualitas);
        }

        $produks = $query->get();
        $kategoris = Kategori::all();
        $kualitass = Kualitas::all();

        return view('home', compact('produks', 'kategoris', 'kualitass'));
    }

    public function detail($id)
    {
        $produk = Produk::with(['kategori', 'kualitas', 'batches' => function ($q) {
            $q->where('stok_sisa', '>', 0)->orderBy('tgl_masuk', 'asc');
        }])->findOrFail($id);

        return view('produk_detail', compact('produk'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'tb_produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;

    protected $fillable = [
        'id_kategori',
        'id_kualitas',
        'nama_produk',
        'deskripsi',
        'satuan',
        'harga',
        'stok',
        'gambar',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function kualitas(): BelongsTo
    {
        return $this->belongsTo(Kualitas::class, 'id_kualitas', 'id_kualitas');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(BatchBeras::class, 'id_produk', 'id_produk');
    }

    public function pasokans(): HasMany
    {
        return $this->hasMany(Pasokan::class, 'id_produk', 'id_produk');
    }

    public function penjualanDetails(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'id_produk', 'id_produk');
    }
}

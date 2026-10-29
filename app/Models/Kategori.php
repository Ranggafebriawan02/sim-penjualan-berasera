<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'tb_kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'deskripsi_kategori',
    ];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id_kategori');
    }
}

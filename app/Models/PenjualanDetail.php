<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenjualanDetail extends Model
{
    protected $table = 'tb_penjualan_detail';
    protected $primaryKey = 'id_penjualan_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_penjualan',
        'id_produk',
        'jumlah',
        'harga_satuan',
        'sub_total',
        'status_detail',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function alokasiBatches(): HasMany
    {
        return $this->hasMany(PengeluaranBatch::class, 'id_penjualan_detail', 'id_penjualan_detail');
    }
}

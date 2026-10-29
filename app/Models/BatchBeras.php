<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchBeras extends Model
{
    protected $table = 'tb_batch_beras';
    protected $primaryKey = 'id_batch';
    public $timestamps = false;

    protected $fillable = [
        'id_produk',
        'kode_batch',
        'tgl_masuk',
        'tgl_panen_giling',
        'tgl_kadaluarsa',
        'stok_awal',
        'stok_sisa',
        'harga_pokok_beli',
        'status_batch',
    ];

    protected function casts(): array
    {
        return [
            'tgl_masuk' => 'date',
            'tgl_panen_giling' => 'date',
            'tgl_kadaluarsa' => 'date',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function pasokans(): HasMany
    {
        return $this->hasMany(Pasokan::class, 'id_batch', 'id_batch');
    }

    public function pengeluaranBatches(): HasMany
    {
        return $this->hasMany(PengeluaranBatch::class, 'id_batch', 'id_batch');
    }

    /**
     * Scope untuk mengambil antrean batch aktif berurutan FIFO (tertua masuk duluan)
     */
    public function scopeFifoQueue($query, $idProduk)
    {
        return $query->where('id_produk', $idProduk)
                     ->where('stok_sisa', '>', 0)
                     ->orderBy('tgl_masuk', 'asc')
                     ->orderBy('id_batch', 'asc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pasokan extends Model
{
    protected $table = 'tb_pasokan';
    protected $primaryKey = 'id_pasokan';
    public $timestamps = false;

    protected $fillable = [
        'id_pemasok',
        'id_produk',
        'id_batch',
        'tgl_pasok',
        'jumlah_kg',
        'harga_per_kg',
        'total_bayar',
        'status_pembayaran_petani',
        'id_user',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pasok' => 'date',
        ];
    }

    public function pemasok(): BelongsTo
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok', 'id_pemasok');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BatchBeras::class, 'id_batch', 'id_batch');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

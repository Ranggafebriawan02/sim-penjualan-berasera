<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'tb_pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'id_penjualan',
        'gateway_provider', // 'midtrans', 'cash'
        'transaction_id',
        'metode_pembayaran', // 'qris', 'bank_transfer', 'cash', dll.
        'va_number',
        'snap_token',
        'nominal_bayar',
        'status_pembayaran', // 'pending', 'settlement', 'expire', 'cancel', 'deny'
        'waktu_transaksi',
        'waktu_bayar',
        'payload_callback',
        'id_user', // bendahara jika bayar tunai
    ];

    protected function casts(): array
    {
        return [
            'waktu_transaksi' => 'datetime',
            'waktu_bayar' => 'datetime',
        ];
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

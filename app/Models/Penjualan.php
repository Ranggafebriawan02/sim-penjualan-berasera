<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penjualan extends Model
{
    protected $table = 'tb_penjualan';
    protected $primaryKey = 'id_penjualan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_penjualan',
        'tgl_jual',
        'id_customer',
        'total',
        'ongkir',
        'poin_digunakan',
        'diskon_poin',
        'poin_didapat',
        'note',
        'alamat_pengiriman',
        'status_penjualan',
        'metode_transaksi', // 'midtrans_qris', 'midtrans_va', 'cash'
        'id_pengiriman',
        'id_user',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    public function pengiriman(): BelongsTo
    {
        return $this->belongsTo(Pengiriman::class, 'id_pengiriman', 'id_pengiriman');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }

    public function detailPenjualans(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_penjualan', 'id_penjualan');
    }

    public function riwayatPoin(): HasOne
    {
        return $this->hasOne(RiwayatPoin::class, 'id_penjualan', 'id_penjualan');
    }
}

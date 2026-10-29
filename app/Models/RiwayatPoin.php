<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPoin extends Model
{
    protected $table = 'tb_riwayat_poin';
    protected $primaryKey = 'id_riwayat_poin';
    public $timestamps = false;

    protected $fillable = [
        'id_customer',
        'id_penjualan',
        'jenis_mutasi', // 'masuk', 'keluar'
        'jumlah_poin',
        'saldo_akhir',
        'keterangan',
        'tgl_mutasi',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mutasi' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}

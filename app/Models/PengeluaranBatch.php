<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengeluaranBatch extends Model
{
    protected $table = 'tb_pengeluaran_batch';
    protected $primaryKey = 'id_pengeluaran_batch';
    public $timestamps = false;

    protected $fillable = [
        'id_penjualan_detail',
        'id_batch',
        'jumlah_keluar',
        'hpp_satuan',
        'subtotal_hpp',
    ];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(PenjualanDetail::class, 'id_penjualan_detail', 'id_penjualan_detail');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BatchBeras::class, 'id_batch', 'id_batch');
    }
}

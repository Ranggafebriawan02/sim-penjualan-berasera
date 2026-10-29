<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengiriman extends Model
{
    protected $table = 'tb_pengiriman';
    protected $primaryKey = 'id_pengiriman';
    public $timestamps = false;

    protected $fillable = [
        'nama_pengiriman',
        'minimal_ongkir',
    ];

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'id_pengiriman', 'id_pengiriman');
    }
}

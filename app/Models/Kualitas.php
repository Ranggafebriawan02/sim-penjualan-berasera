<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kualitas extends Model
{
    protected $table = 'tb_kualitas';
    protected $primaryKey = 'id_kualitas';
    public $timestamps = false;

    protected $fillable = [
        'nama_kualitas',
        'standar_butir_patah',
        'kadar_air_maks',
        'derajat_sosoh_min',
        'keterangan_kemasan',
    ];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_kualitas', 'id_kualitas');
    }
}

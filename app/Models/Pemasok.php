<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemasok extends Model
{
    protected $table = 'tb_pemasok';
    protected $primaryKey = 'id_pemasok';
    public $timestamps = false;

    protected $fillable = [
        'nama_pemasok',
        'telepon',
        'alamat',
        'rekening_bank',
        'status_pemasok',
    ];

    public function pasokans(): HasMany
    {
        return $this->hasMany(Pasokan::class, 'id_pemasok', 'id_pemasok');
    }
}

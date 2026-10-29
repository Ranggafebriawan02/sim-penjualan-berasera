<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'tb_customer';
    protected $primaryKey = 'id_customer';
    public $timestamps = false;

    protected $fillable = [
        'nama_customer',
        'alamat',
        'telepon',
        'email',
        'username',
        'password',
        'total_poin',
        'tier_membership', // Silver, Gold, Platinum
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'id_customer', 'id_customer');
    }

    public function riwayatPoins(): HasMany
    {
        return $this->hasMany(RiwayatPoin::class, 'id_customer', 'id_customer');
    }
}

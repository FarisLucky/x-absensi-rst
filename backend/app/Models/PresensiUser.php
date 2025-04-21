<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PresensiUser extends Model
{
    protected $table = "presensi_user";

    protected $fillable = [
        'id',
        'nip',
        'tanggal',
        'id_jadwal',
    ];

    public function jadwal(): HasOne
    {
        return $this->hasOne(Jadwal::class, 'id', 'id_jadwal');
    }

    public function mKaryawan(): HasOne
    {
        return $this->hasOne(MKaryawan::class, 'nip', 'nip');
    }
}

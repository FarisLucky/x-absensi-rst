<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiTerlambat extends Model
{

    const MASUK = 'TELAT';
    protected $table = "presensi_terlambat";

    protected $fillable = [
        'id_jadwal',
        'jenis',
        'ket',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

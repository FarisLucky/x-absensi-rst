<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    const TEPAT = 'TEPAT';
    const TELAT = 'TELAT';
    protected $table = "presensi";

    protected $fillable = [
        'id',
        'id_jadwal',
        'nama',
        'latlng_masuk',
        'latlng_pulang',
        'manufact',
        'model',
        'platform',
        'osVersion',
        'ip',
        'lok_masuk',
        'lok_pulang',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'id_jadwal',
            'nama',
            'latlng_masuk',
            'latlng_pulang',
            'manufact',
            'model',
            'platform',
            'osVersion',
            'ip',
            'lok_masuk',
            'lok_pulang',
        );
    }
}

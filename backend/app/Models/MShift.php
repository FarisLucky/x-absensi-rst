<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MShift extends Model
{
    protected $table = "m_shift";

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'mulai_absen',
        'jam_masuk',
        'telat_masuk',
        'jam_pulang',
        'telat_pulang',
    ];

    public $casts = [
        'mulai_absen' => 'integer',
        'telat_masuk' => 'integer',
        'telat_pulang' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'kode',
            'nama',
            'mulai_absen',
            'jam_masuk',
            'telat_masuk',
            'jam_pulang',
            'telat_pulang',
        );
    }
}

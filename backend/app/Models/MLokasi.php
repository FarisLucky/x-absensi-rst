<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLokasi extends Model
{
    const NONAKTIF = 0;
    const AKTIF = 1;

    protected $table = "m_lokasi";

    protected $fillable = [
        'id',
        'nama',
        'latitude',
        'longitude',
        'radius',
        'status',
    ];

    public $casts = [
        'radius' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'nama',
            'latitude',
            'longitude',
            'radius',
            'status',
        );
    }
}

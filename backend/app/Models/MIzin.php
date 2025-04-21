<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MIzin extends Model
{
    protected $table = "m_izin";

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'tahunan',
        'acc_manajemen',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'kode',
            'nama',
            'tahunan',
            'acc_manajemen',
        );
    }
}

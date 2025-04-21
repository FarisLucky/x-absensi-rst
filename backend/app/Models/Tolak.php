<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tolak extends Model
{
    const IZIN = 'IZIN';
    protected $table = "tolak";

    protected $fillable = [
        'id',
        'id_relation',
        'ket',
        'jenis',
        'created_by',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'id_relation',
            'ket',
            'jenis',
            'created_by',
        );
    }
}

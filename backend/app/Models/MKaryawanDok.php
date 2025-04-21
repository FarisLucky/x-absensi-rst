<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MKaryawanDok extends Model
{
    protected $table = "m_karyawandok";

    protected $fillable = [
        'id_karyawan',
        'id_jenis',
        'nip',
        'jenis',
        'file',
        'size',
        'ket',
    ];

    public $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id_karyawan',
            'nip',
            'id_jenis',
            'jenis',
            'file',
            'size',
            'ket',
        );
    }
}

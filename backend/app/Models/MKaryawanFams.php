<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MKaryawanFams extends Model
{

    protected $table = "m_karyawan_fams";

    protected $fillable = [
        'id_karyawan',
        'ibu',
        'pasangan',
        'nama_pasangan',
        'nik',
        'tempat_lahir',
        'tgl_lahir',
        'ank1',
        'nik_ank1',
        'tempat_lahir_ank1',
        'tgl_lahir_ank1',
        'ank2',
        'nik_ank2',
        'tempat_lahir_ank2',
        'tgl_lahir_ank2',
        'ank3',
        'nik_ank3',
        'tempat_lahir_ank3',
        'tgl_lahir_ank3',
        'ank4',
        'nik_ank4',
        'tempat_lahir_ank4',
        'tgl_lahir_ank4',
    ];

    public $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id_karyawan',
            'ibu',
            'pasangan',
            'nama_pasangan',
            'nik',
            'tempat_lahir',
            'tgl_lahir',
            'ank1',
            'nik_ank1',
            'tempat_lahir_ank1',
            'tgl_lahir_ank1',
            'ank2',
            'nik_ank2',
            'tempat_lahir_ank2',
            'tgl_lahir_ank2',
            'ank3',
            'nik_ank3',
            'tempat_lahir_ank3',
            'tgl_lahir_ank3',
            'ank4',
            'nik_ank4',
            'tempat_lahir_ank4',
            'tgl_lahir_ank4',
        );
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(MKaryawan::class, 'id_karyawan', 'id');
    }
}

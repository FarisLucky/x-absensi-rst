<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinDetail extends Model
{
    use \Awobaz\Compoships\Compoships;

    protected $table = "izin_detail";

    protected $fillable = [
        'id',
        'id_izin',
        'tanggal',
        'nip',
        'pengganti',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'id_izin',
            'tanggal',
            'nip',
            'pengganti',
        );
    }

    public function izin(): BelongsTo
    {
        return $this->belongsTo(Izin::class, 'id_izin', 'id');
    }

    public function izinCuti(): BelongsTo
    {
        return $this->belongsTo(IzinCuti::class, 'id_izin', 'id_izin');
    }
    public function penggantiBy(): BelongsTo
    {
        return $this->belongsTo(MKaryawan::class, 'pengganti', 'nip')->select('nip', 'nama');
    }
}

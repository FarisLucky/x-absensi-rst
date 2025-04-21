<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IzinBukti extends Model
{
    protected $table = "izin_bukti";

    protected $fillable = [
        'id',
        'id_izin',
        'nama',
        'ext',
        'path',
        'disk',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['bukti_url'];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'id_izin',
            'nama',
            'ext',
            'path',
            'disk',
        );
    }

    public function getBuktiUrlAttribute()
    {
        $path = $this->path . '/' . $this->nama;

        return Storage::disk('public')->url($path);
    }
}

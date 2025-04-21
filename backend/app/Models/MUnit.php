<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MUnit extends Model
{

    protected $table = "m_unit";

    protected $fillable = [
        'id',
        'nama',
        'id_parent',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select('id', 'nama', 'id_parent');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'id_unit', 'id');
    }

    public function karyawan(): HasMany
    {
        return $this->hasMany(MKaryawan::class, 'id_unit', 'id');
    }
}

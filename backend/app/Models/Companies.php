<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Companies extends Model
{
    protected $table = "perusahaan";

    protected $fillable = [
        'nama',
        'short',
        'logo',
        'alamat',
        'telp',
        'email',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'nama',
            'short',
            'logo',
            'alamat',
            'telp',
            'email',
        );
    }

    public function getPhotoUrlCastAttribute()
    {
        return !is_null($this->photo) ? Storage::disk('public')->url('logopt/' . $this->photo) : $this->photo;
    }
}

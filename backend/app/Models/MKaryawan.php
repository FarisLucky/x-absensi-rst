<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class MKaryawan extends Model
{
    protected $table = "m_karyawan";

    protected $fillable = [
        'id',
        'id_unit',
        'unit',
        'jabatan',
        'nip',
        'nik',
        'nama',
        'sex',
        'tempat_lahir',
        'tgl_lahir',
        'pendidikan',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kec',
        'kab',
        'prov',
        'kodepos',
        'telp',
        'tgl_masuk',
        'tgl_resign',
        'email',
        'jml_anak',
        'jml_cuti',
        'status_kerja',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'id_unit',
            'unit',
            'jabatan',
            'nip',
            'nik',
            'nama',
            'sex',
            'tempat_lahir',
            'tgl_lahir',
            'pendidikan',
            'alamat',
            'rt',
            'rw',
            'desa',
            'kec',
            'kab',
            'kodepos',
            'prov',
            'telp',
            'tgl_masuk',
            'tgl_resign',
            'email',
            'jml_anak',
            'jml_cuti',
            'status_kerja',
            'photo'
        );
    }

    protected $appends = ['tgl_lahir_cast', 'photo_url_cast'];

    public function getTglLahirCastAttribute()
    {
        return !is_null($this->tgl_lahir) && strpos($this->tgl_lahir, '-') ? Carbon::make($this->tgl_lahir)->format('d-m-Y') : $this->tgl_lahir;
    }

    public function getPhotoUrlCastAttribute()
    {
        return !is_null($this->photo) ? Storage::disk('public')->url('profil/' . $this->photo) : $this->photo;
    }

    public function scopeResign($query)
    {
        return $query->whereNotNull('tgl_resign')->orWhere('tgl_resign', '<>', '');
    }

    public function scopeNotResign($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('tgl_resign')->orWhere('tgl_resign', '');
        });
    }

    public function mUnit(): HasOne
    {
        return $this->hasOne(MUnit::class, 'id', 'id_unit');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'nip', 'nip');
    }

    /**
     * rekapan
     */

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'nip', 'nip');
    }
}

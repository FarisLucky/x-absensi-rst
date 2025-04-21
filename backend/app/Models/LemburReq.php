<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LemburReq extends Model
{
    const PENGAJUAN = 0;
    const DISETUJUI = 1;
    const DITOLAK = 2;

    protected $table = "lembur_req";

    protected $fillable = [
        'id',
        'nip',
        'nama',
        'unit',
        'tanggal',
        'mulai',
        'akhir',
        'ttl_jam',
        'ttl_menit',
        'status',
        'catatan',
    ];
    protected $appends = ['tanggal_cast', 'tgl_day_cast', 'mulai_cast', 'akhir_cast', 'created_at_cast', 'status_desc'];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'nip',
            'nama',
            'unit',
            'tanggal',
            'mulai',
            'akhir',
            'ttl_jam',
            'ttl_menit',
            'status',
            'catatan',
            'created_at',
        );
    }

    public function getTanggalCastAttribute()
    {
        return !is_null($this->tanggal) ? Carbon::make($this->tanggal)->format('d-m-Y') : $this->tanggal;
    }

    // JOIN CASTING
    public function getAccAtCastAttribute()
    {
        return !is_null($this->acc_at) ? Carbon::make($this->acc_at)->isoFormat('ll HH:mm') : $this->acc_at;
    }

    public function getTglDayCastAttribute()
    {
        return !is_null($this->tanggal) ? Carbon::make($this->tanggal)->isoFormat('dddd') : $this->tanggal;
    }

    public function getMulaiCastAttribute()
    {
        return !is_null($this->mulai) ? Carbon::make($this->mulai)->format('H:i') : $this->mulai;
    }

    public function getAkhirCastAttribute()
    {
        return !is_null($this->akhir) ? Carbon::make($this->akhir)->format('H:i') : $this->akhir;
    }

    public function getCreatedAtCastAttribute()
    {
        return !is_null($this->created_at) ? $this->created_at->isoFormat('ll HH:mm') : $this->created_at;
    }

    public function getDayCastAttribute()
    {
        return !is_null($this->mulai) ? Carbon::make($this->mulai)->isoFormat('dddd') : $this->mulai;
    }

    public function getStatusDescAttribute()
    {
        $result = "";
        switch ($this->status) {
            case self::PENGAJUAN:
                $result = "PENGAJUAN";
                break;
            case self::DISETUJUI:
                $result = "DISETUJUI";
                break;
            case self::DITOLAK:
                $result = "DITOLAK";
                break;
        }

        return $result;
    }

    public function lemburApprov()
    {
        return $this->hasOne(LemburApprov::class, 'id_lembur', 'id');
    }

    public function pemohon()
    {
        return $this->belongsTo(MKaryawan::class, 'nip', 'nip');
    }
}

<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Model;
use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Izin extends Model
{
    use \Awobaz\Compoships\Compoships;
    const PENGAJUAN = 0;

    const PROGRESS = 1;

    const SELESAI = 2;

    const DITOLAK = 3;

    const BATAL = 4;

    protected $table = "izin";

    protected $fillable = [
        'id',
        'nip',
        'nama',
        'unit',
        'jabatan',
        'idm_izin',
        'kode_izin',
        'izin',
        'mulai',
        'akhir',
        'masuk',
        'periode',
        'cuti_diambil',
        'sisa',
        'ket',
        'acc_nama',
        'acc_nip',
        'acc_at',
        'created_by',
        'acc_status',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['mulai_cast', 'akhir_cast', 'masuk_cast', 'acc_at_cast', 'acc_status_desc', 'created_at_cast', 'mulai_day_cast', 'akhir_day_cast', 'masuk_day_cast'];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'nip',
            'nama',
            'unit',
            'jabatan',
            'idm_izin',
            'kode_izin',
            'izin',
            'mulai',
            'akhir',
            'masuk',
            'periode',
            'cuti_diambil',
            'sisa',
            'ket',
            'acc_nama',
            'acc_nip',
            'acc_at',
            'created_by',
            'acc_status',
            'created_at',
        );
    }

    public function getMulaiCastAttribute()
    {
        return !is_null($this->mulai) ? Carbon::make($this->mulai)->format('d-m-Y') : $this->mulai;
    }

    public function getAkhirCastAttribute()
    {
        return !is_null($this->akhir) ? Carbon::make($this->akhir)->format('d-m-Y') : $this->akhir;
    }

    public function getCreatedAtCastAttribute()
    {
        return !is_null($this->created_at) ? $this->created_at->isoFormat('ll HH:mm') : $this->created_at;
    }

    public function getMulaiDayCastAttribute()
    {
        return !is_null($this->mulai) ? Carbon::make($this->mulai)->isoFormat('dddd') : $this->mulai;
    }

    public function getAkhirDayCastAttribute()
    {
        return !is_null($this->akhir) ? Carbon::make($this->akhir)->isoFormat('dddd') : $this->akhir;
    }

    public function getMasukDayCastAttribute()
    {
        return !is_null($this->masuk) ? Carbon::make($this->masuk)->isoFormat('dddd') : $this->masuk;
    }

    public function getMasukCastAttribute()
    {
        return !is_null($this->masuk) ? Carbon::make($this->masuk)->format('d-m-Y') : $this->masuk;
    }

    public function getAccAtCastAttribute()
    {
        return !is_null($this->acc_at) ? Carbon::make($this->acc_at)->format('d-m-Y H:i') : $this->acc_at;
    }

    public function getAccStatusDescAttribute()
    {
        $result = "";
        switch ($this->acc_status) {
            case self::PENGAJUAN:
                $result = "PENGAJUAN";
                break;
            case self::PROGRESS:
                $result = "PROGRESS";
                break;
            case self::SELESAI:
                $result = "SELESAI";
                break;
            case self::DITOLAK:
                $result = "DITOLAK";
                break;
            case self::BATAL:
                $result = "BATAL";
                break;
        }

        return $result;
    }

    public function mIzin(): BelongsTo
    {
        return $this->belongsTo(MIzin::class, 'idm_izin');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(MKaryawan::class, 'nip', 'nip');
    }

    public function izinDetail(): HasMany
    {
        return $this->hasMany(IzinDetail::class, 'id_izin', 'id');
    }

    public function izinBukti(): HasMany
    {
        return $this->hasMany(IzinBukti::class, 'id_izin', 'id');
    }

    public function tolak(): BelongsTo
    {
        return $this->belongsTo(Tolak::class, 'id_izin', 'id');
    }
}

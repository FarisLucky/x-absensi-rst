<?php

namespace App\Models;

use Carbon\Carbon;
use Awobaz\Compoships\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Jadwal extends Model
{
    use \Awobaz\Compoships\Compoships;

    const PRESEN_TEPAT = 'TEPAT';
    const PRESEN_TELAT = 'TELAT';
    const BELUM = 0;
    const PROGRESS = 1;
    const SELESAI = 2;
    const TIDAK_HADIR = 3;
    const IZIN = 4;
    const AUTOPLG = 1;

    protected $table = "jadwal";

    protected $fillable = [
        'id',
        'tanggal',
        'nip',
        'nama',
        'id_unit',
        'unit',
        'jabatan',
        'kode_shift',
        'shift',
        'mulai_absen',
        'jam_masuk',
        'telat_masuk',
        'jam_pulang',
        'telat_pulang',
        'libur',
        'created_by',
        'status',
        'status_absen',
        'masuk',
        'pulang',
        'auto',
        'tgl_masuk',
        'tgl_pulang',
        'ttlkerja',
        'ttltelat',
    ];

    public $casts = [
        'libur' => 'integer',
        'mulai_absen' => 'integer',
        'telat_masuk' => 'integer',
        'telat_pulang' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['tanggal_cast', 'tanggal_for_human', 'status_cast', 'mulai_absen_cast', 'day_cast'];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'tanggal',
            'nip',
            'nama',
            'id_unit',
            'unit',
            'jabatan',
            'kode_shift',
            'shift',
            'mulai_absen',
            'jam_masuk',
            'telat_masuk',
            'jam_pulang',
            'telat_pulang',
            'libur',
            'created_by',
            'status',
            'status_absen',
            'masuk',
            'pulang',
            'auto',
            'tgl_masuk',
            'tgl_pulang',
            'ttlkerja',
            'ttltelat',
        );
    }

    public function getTanggalCastAttribute()
    {
        return !is_null($this->tanggal) ? Carbon::make($this->tanggal)->format('d-m-Y') : $this->tanggal;
    }

    public function getDayCastAttribute()
    {
        return !is_null($this->tanggal) ? Carbon::make($this->tanggal)->isoFormat('dddd') : $this->tanggal;
    }

    public function getTanggalForHumanAttribute()
    {
        return !is_null($this->tanggal) ? Carbon::make($this->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') : $this->tanggal;
    }

    public function getMulaiAbsenCastAttribute()
    {
        return !is_null($this->jam_masuk) ? Carbon::createFromFormat('H:i', $this->jam_masuk)->subMinutes($this->mulai_absen)->format('H:i') : $this->jam_masuk;
    }

    public function getStatusCastAttribute()
    {
        $resp = "";
        switch ($this->status) {
            case self::SELESAI:
                $resp = "SELESAI";
                break;
            case self::PROGRESS:
                $resp = "PROGRESS";
                break;
            case self::TIDAK_HADIR:
                $resp = "ALPA";
                break;
            case self::IZIN:
                $resp = "IZIN";
                break;

            default:
                $resp = "BELUM ABSEN";
                break;
        }

        return $resp;
    }

    public function mUnit(): BelongsTo
    {
        return $this->belongsTo(MUnit::class, 'id_unit', 'id');
    }

    public function mKaryawan(): HasOne
    {
        return $this->hasOne(MKaryawan::class, 'nip', 'nip');
    }

    public function presensi(): HasOne
    {
        return $this->hasOne(Presensi::class, 'id_jadwal', 'id');
    }

    public function presensiTerlambat(): HasOne
    {
        return $this->hasOne(PresensiTerlambat::class, 'id_jadwal', 'id');
    }

    public function izin(): HasOne
    {
        return $this->hasOne(Izin::class, 'mulai', 'tanggal');
    }
}

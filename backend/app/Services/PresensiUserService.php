<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\PresensiUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiUserService
{
    public function getJadwal($nip)
    {
        $presensiUser = Jadwal::leftJoin('presensi', 'jadwal.id', '=', 'presensi.id')
            ->leftJoin('izin_detail', 'jadwal.tanggal', '=', 'izin_detail.id')
            ->leftJoin('izin', 'izin_detail.id_izin', '=', 'izin.id')
            ->leftJoin('presensi_terlambat', 'jadwal.id', '=', 'presensi_terlambat.id_jadwal')
            ->select(
                'jadwal.id',
                'jadwal.id',
                'jadwal.nama',
                'jadwal.tanggal',
                'jadwal.shift',
                'jadwal.jam_masuk',
                'jadwal.jam_pulang',
                'jadwal.mulai_absen',
                'jadwal.telat_masuk',
                'jadwal.libur',
                'jadwal.status',
                'jadwal.status_absen',
                'jadwal.masuk',
                'jadwal.pulang',
                'jadwal.ttltelat',
                'izin.kode_izin',
                'presensi_terlambat.ket',
            )
            ->where('jadwal.nip', $nip)
            ->orderByDesc('jadwal.created_at')
            ->first();

        if (!is_null($presensiUser)) {
            $presensiUser->tanggal_cast = Carbon::createFromFormat('Y-m-d', $presensiUser->tanggal)->format('d-m-Y');
            $presensiUser->mulai_absen_cast = Carbon::createFromFormat('H:i', $presensiUser->jam_masuk)->subMinutes($presensiUser->mulai_absen)->format('H:i');
        }

        return $presensiUser;
    }
}

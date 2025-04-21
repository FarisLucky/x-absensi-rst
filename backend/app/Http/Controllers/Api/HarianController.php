<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\MShift;

class HarianController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $tanggal = request('tanggal');
            $search = request('search');
            $shift = request('shift');
            $perPage = request('perPage');
            $getShift = [];

            if (
                $shift === 'PAGI'
            ) {
                $getShift = MShift::whereTime(
                    'jam_masuk',
                    '>',
                    "06:00:00"
                )
                    ->whereTime(
                        'jam_masuk',
                        '<',
                        "10:00:00"
                    )
                    ->pluck('kode');
            } else if (
                $shift === 'SIANG'
            ) {
                $getShift = MShift::whereTime(
                    'jam_masuk',
                    '>',
                    "09:00:00"
                )
                    ->whereTime(
                        'jam_masuk',
                        '<',
                        "13:00:00"
                    )
                    ->pluck('kode');
            } else if (
                $shift === 'MALAM'
            ) {
                $getShift = MShift::whereTime(
                    'jam_masuk',
                    '>',
                    "14:00:00"
                )
                    ->whereTime(
                        'jam_pulang',
                        '<',
                        "08:00:00"
                    )
                    ->pluck('kode');
            }

            $jadwals = Jadwal::with([
                'presensiTerlambat' => function ($query) {
                    $query->select('id', 'id_jadwal', 'jenis', 'ket');
                },
                'mKaryawan' => function ($query) {
                    $query->select('id', 'nip', 'photo');
                },
            ])
                ->leftJoin('presensi', 'presensi.id_jadwal', '=', 'jadwal.id')
                ->select([
                    'jadwal.id',
                    'jadwal.tanggal',
                    'jadwal.nip',
                    'jadwal.nama',
                    'jadwal.unit',
                    'jadwal.shift',
                    'jadwal.jam_masuk',
                    'jadwal.jam_pulang',
                    'jadwal.status',
                    'jadwal.status_absen',
                    'jadwal.masuk',
                    'jadwal.libur',
                    'jadwal.pulang',
                    'jadwal.ttlkerja',
                    'presensi.lok_masuk',
                    'presensi.lok_pulang',
                    'presensi.latlng_masuk',
                    'presensi.latlng_pulang',
                ])
                ->whereIn('kode_shift', $getShift)
                ->whereDate('tanggal', $tanggal)
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('jadwal.nama', 'LIKE', "%{$search}%")
                        ->orWhere('jadwal.nip', 'LIKE', "%{$search}%");
                })
                ->paginate($perPage);

            return $this->okApiResponse($jadwals, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

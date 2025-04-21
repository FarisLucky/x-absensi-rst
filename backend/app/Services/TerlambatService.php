<?php

namespace App\Services;

use App\Models\Presensi;

class TerlambatService
{
    public function findByDateRangeAndUnit(array $params)
    {
        $terlambats = Presensi::with([
            'jadwal' => function ($query) {
                $query->select('id', 'id_unit', 'jam_masuk', 'telat_masuk');
            },
            'jadwal.mUnit' => function ($query) {
                $query->select('id', 'nama');
            },
            'presensiTerlambat' => function ($query) {
                $query->select('id', 'id_presensi', 'jenis', 'ket');
            }
        ])
            ->select([
                'id',
                'id_jadwal',
                'tanggal',
                'nip',
                'nama',
                'masuk',
                'ttltelat',
                'status',
            ])
            ->whereBetween('tanggal', [$params['start'], $params['end']])
            ->when(!is_null($params['id_unit']), function ($query) use ($params) {
                $query->whereHas('jadwal', function ($query) use ($params) {
                    return $query->whereIn('id_unit', $params['id_unit']);
                });
            })
            ->where('status', Presensi::TELAT)
            ->orderBy('nama')
            ->orderBy('tanggal')
            ->get();
        // dd($terlambats->first());

        return $terlambats;
    }
}

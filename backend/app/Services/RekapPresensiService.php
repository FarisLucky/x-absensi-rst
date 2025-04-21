<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\IzinDetail;
use App\Models\Jadwal;
use Illuminate\Support\Collection;

class RekapPresensiService
{
    public function getPresensi(array $data)
    {
        $presensies =  Jadwal::leftJoin('presensi', 'presensi.id_jadwal', '=', 'jadwal.id')
            ->join('m_karyawan', 'm_karyawan.nip', '=', 'jadwal.nip')
            ->join('m_unit', 'm_unit.id', '=', 'm_karyawan.id_unit')
            ->leftJoin('izin_detail', function ($query) {
                $query->on('izin_detail.tanggal', '=', 'jadwal.tanggal');
                $query->on('izin_detail.nip', '=', 'jadwal.nip');
            })
            ->leftJoin('izin', function ($query) {
                $query->on('izin_detail.id_izin', '=', 'izin.id');
            })
            ->selectRaw('jadwal.id, jadwal.tanggal, jadwal.nip, jadwal.kode_shift, m_karyawan.nama, jadwal.shift, jadwal.jam_masuk, jadwal.jam_pulang, jadwal.masuk, jadwal.pulang, jadwal.status_absen, jadwal.status, m_unit.nama as nama_unit, izin_detail.tanggal as izin_detail_tanggal, jadwal.libur, izin.izin, presensi.nama as device, presensi.lok_masuk, presensi.lok_pulang')
            // filter month & year
            ->when(
                !is_null(optional($data)['month']) &&
                    !is_null(optional($data)['year']),
                function ($query) use ($data) {
                    $query->where(function ($query)  use ($data) {
                        $query->whereMonth('jadwal.tanggal', $data['month'])
                            ->whereYear('jadwal.tanggal', $data['year']);
                    });
                }
            )
            // filter tanggal
            ->when(!is_null(optional($data)['tgl']), function ($query) use ($data) {
                $query->whereDate('jadwal.tanggal', $data['tgl']);
            })
            // filter unit
            // ->when(!is_null(optional($data)['unit']), function ($query) use ($data) {
            //     $query->where('jadwal.id_unit', $data['unit']);
            // })
            ->where(function ($query) {
                $query->whereNull('m_karyawan.tgl_resign')
                    ->orWhere('m_karyawan.tgl_resign', '');
            })
            ->orderBy('jadwal.shift')
            ->orderBy('m_unit.nama')
            ->orderBy('m_karyawan.nama')
            // ->orderBy('jadwal.tanggal')
            ->get();

        return $presensies;
    }

    public function ttlPresensi(array $data, Collection $karyawans)
    {
        $range = $data['range'];
        $jenis = $data['jenis'];
        $data = [];

        $jadwalQuery = Jadwal::selectRaw('COUNT(*) as ttl')
            ->when($jenis === 'BULANAN', function ($query) use ($range) {
                $split = explode('-', $range);
                $query->whereMonth('tanggal', $split[0])->whereYear('tanggal', $split[1]);
            })
            ->when($jenis === 'TAHUNAN', function ($query) use ($range) {
                $query->whereYear('tanggal', $$range);
            });

        foreach ($karyawans as $karyawan) {
            $data[$karyawan->nama]['ttl_kerja'] = Jadwal::selectRaw('SUM(ttlkerja) as ttl')
                ->when($jenis === 'BULANAN', function ($query) use ($range) {
                    $split = explode('-', $range);
                    $query->whereMonth('tanggal', $split[0])->whereYear('tanggal', $split[1]);
                })
                ->when($jenis === 'TAHUNAN', function ($query) use ($range) {
                    $query->whereYear('tanggal', $$range);
                })
                ->where('nip', $karyawan->nip)
                ->pluck('ttl')
                ->first();

            $data[$karyawan->nama]['unit'] = $karyawan->mUnit->nama;

            $data[$karyawan->nama]['jadwal'] = $jadwalQuery->clone()
                ->where('nip', $karyawan->nip)
                ->first();

            $data[$karyawan->nama]['libur'] = $jadwalQuery->clone()
                ->where(function ($query) use ($karyawan) {
                    $query->whereNull('kode_shift')
                        ->where('libur', 1);
                })
                ->where('nip', $karyawan->nip)
                ->first();

            $data[$karyawan->nama]['alpa'] = $jadwalQuery->clone()
                ->where(function ($query) use ($karyawan) {
                    $query->where('status', Jadwal::TIDAK_HADIR)
                        ->where('libur', 0);
                })
                ->where('nip', $karyawan->nip)
                ->first();

            $data[$karyawan->nama]['cuti'] = IzinDetail::selectRaw('COUNT(id) as ttl')
                ->when($jenis === 'BULANAN', function ($query) use ($range) {
                    $split = explode('-', $range);
                    $query->whereMonth('tanggal', $split[0])->whereYear('tanggal', $split[1]);
                })
                ->when($jenis === 'TAHUNAN', function ($query) use ($range) {
                    $query->whereYear('tanggal', $$range);
                })
                ->whereHas('izin', function ($query) use ($karyawan) {
                    $query->where('acc_status', Izin::SELESAI);
                })
                ->where('nip', $karyawan->nip)
                ->first();

            $data[$karyawan->nama]['hadir'] = $jadwalQuery->clone()
                ->where(function ($query) use ($karyawan) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::PROGRESS);
                })
                ->where('nip', $karyawan->nip)
                ->first();

            $data[$karyawan->nama]['tepat'] = Jadwal::selectRaw('COUNT(id) as ttl')
                ->when($jenis === 'BULANAN', function ($query) use ($range) {
                    $split = explode('-', $range);
                    $query->whereMonth('tanggal', $split[0])
                        ->whereYear('tanggal', $split[1]);
                })
                ->when($jenis === 'TAHUNAN', function ($query) use ($range) {
                    $query->whereYear('tanggal', $$range);
                })
                ->where(function ($query) use ($karyawan) {
                    $query->where('status', Jadwal::PRESEN_TEPAT)
                        ->where('nip', $karyawan->nip);
                })
                ->first();

            $data[$karyawan->nama]['telat'] = Jadwal::selectRaw('COUNT(id) as ttl')
                ->when($jenis === 'BULANAN', function ($query) use ($range) {
                    $split = explode('-', $range);
                    $query->whereMonth('tanggal', $split[0])->whereYear('tanggal', $split[1]);
                })
                ->when($jenis === 'TAHUNAN', function ($query) use ($range) {
                    $query->whereYear('tanggal', $$range);
                })
                ->where(function ($query) use ($karyawan) {
                    $query->where('status', Jadwal::PRESEN_TELAT)
                        ->where('nip', $karyawan->nip);
                })
                ->first();
        }

        return $data;
    }
}

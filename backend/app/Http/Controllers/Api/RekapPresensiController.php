<?php

namespace App\Http\Controllers\Api;

use App\Exports\IzinExport;
use App\Exports\PresensiBulananExport;
use App\Exports\PresensiExport;
use App\Exports\RekapPresensiBulananExport;
use App\Exports\TukarShiftExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\MIzin;
use App\Models\MKaryawan;
use App\Models\MUnit;
use App\Models\Presensi;
use App\Models\TukarJadwal;
use App\Models\User;
use App\Services\PresensiService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class RekapPresensiController extends Controller
{
    use ApiResponse;

    private PresensiService $presensiService;

    public function __construct(PresensiService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    public function exportExcel()
    {
        try {
            $tgl = Carbon::make(request('tanggal'));

            return Excel::download(new PresensiExport($tgl), "presensi pada {$tgl->format('d-m-Y')}.xlsx");
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function exportPresensiBulanan()
    {
        try {
            $month = request('month');
            $year = request('year');
            $user = auth()->user();
            $unit = !is_null(request('unit')) ? request('unit') : $user->mKaryawan->id_unit;

            $payload = [
                'month' => $month,
                'year' => $year,
                'unit' => $unit,
                'user' => $user,
            ];

            return Excel::download(new PresensiBulananExport($payload), "presensi pada {$month}-{$year}.xlsx");
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function rekapPresensiBulanan()
    {
        try {
            $range = request('range');
            $unit = request('unit');
            $jenis = request('jenis');

            $payload = [
                'range' => $range,
                'unit' => $unit,
                'user' => null,
                'jenis' => $jenis
            ];

            return Excel::download(new RekapPresensiBulananExport($payload), "rekap presensi {$jenis} pada {$range}.xlsx");
        } catch (\Throwable $th) {

            // return $this->errorApiResponse($th->getTraceAsString());
            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function exportTukarJadwalExcel()
    {
        try {
            $rangeTanggal = request('range_tanggal');
            $search = request('search');
            $jenis = request('jenis');
            $unit = request('unit');

            $payload = [
                'range_tanggal' => $rangeTanggal,
                'search' => $search,
                'jenis' => $jenis,
                'unit' => $unit,
            ];

            return Excel::download(new TukarShiftExport($payload), "rekap tukar jadwal {$rangeTanggal}-{$unit}-{$jenis}-{$search}.xlsx");
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function exportIzinExcel()
    {
        try {
            $rangeTanggal = request('range_tanggal');
            $search = request('search');
            $izin = request('izin');
            $unit = request('unit');

            $payload = [
                'range_tanggal' => $rangeTanggal,
                'search' => $search,
                'izin' => $izin,
                'unit' => $unit,
            ];

            return Excel::download(new IzinExport($payload), "rekap izin {$rangeTanggal}-{$unit}-{$izin}-{$search}.xlsx");
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function rekapKehadiran()
    {
        try {
            $rangeTanggal = request('range_tanggal');
            $status = request('status');
            $units = request('unit');
            $lists = null;
            $all = false;
            foreach ($units as $unit) {
                if (intval($unit) === -1) {
                    $all = true;
                    break;
                }
            }

            $getUnit = MUnit::whereHas('jadwal')
                ->when(count($units) > 0 && !$all, function ($query) use ($units) {
                    $query->whereIn('id', $units);
                })
                ->get([
                    'id',
                    'nama'
                ]);

            switch ($status) {
                case 'TL':
                    $lists = Jadwal::selectRaw('jadwal.id, COUNT(*) AS ttl, jadwal.nip, jadwal.nama, jadwal.unit')
                        ->when(!is_null($rangeTanggal), function ($query) use ($rangeTanggal) {
                            $query->where(function ($query) use ($rangeTanggal) {
                                $tgl = explode('to', $rangeTanggal);
                                $start = Carbon::make($tgl[0])->format('Y-m-d');
                                $end = Carbon::make($tgl[1])->format('Y-m-d');
                                $query->whereBetween('jadwal.tanggal', [$start, $end]);
                            });
                        })
                        ->whereIn('jadwal.id_unit', $getUnit->pluck('id'))
                        ->where('jadwal.status_absen', Presensi::TELAT)
                        ->groupBy('jadwal.nip')
                        ->orderByDesc('ttl')
                        ->get();
                    break;
                case 'A':
                    $lists = Jadwal::selectRaw('jadwal.id, COUNT(*) AS ttl, jadwal.nip, jadwal.nama, jadwal.unit')
                        ->when(!is_null($rangeTanggal), function ($query) use ($rangeTanggal) {
                            $query->where(function ($query) use ($rangeTanggal) {
                                $tgl = explode('to', $rangeTanggal);
                                $start = Carbon::make($tgl[0])->format('Y-m-d');
                                $end = Carbon::make($tgl[1])->format('Y-m-d');
                                $query->whereBetween('jadwal.tanggal', [$start, $end]);
                            });
                        })
                        ->whereIn('jadwal.id_unit', $getUnit->pluck('id'))
                        ->where('jadwal.status', Jadwal::TIDAK_HADIR)
                        ->groupBy('jadwal.nip')
                        ->orderByDesc('ttl')
                        ->get();
                    break;
            }

            return $this->okApiResponse(
                $lists,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

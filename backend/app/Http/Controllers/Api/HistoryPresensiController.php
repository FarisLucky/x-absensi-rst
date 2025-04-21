<?php

namespace App\Http\Controllers\Api;

use App\Exports\PresensiHarianExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\User;
use App\Services\PresensiService;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class HistoryPresensiController extends Controller
{
    use ApiResponse;

    private $presensiService;

    public function __construct(PresensiService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    public function index()
    {
        try {

            $user = auth()->user();
            $bawahanFilter = request('bawahan');
            $month = request('month');
            $year = request('year');
            $unit = request('unit');

            $presensi = Jadwal::with([
                'presensi' => function ($query) {
                    $query->select('id', 'id_jadwal', 'status', 'masuk', 'pulang', 'tgl_pulang', 'ttltelat');
                },
                'mKaryawan' => function ($query) {
                    $query->select('nip', 'nama', 'photo', 'id_unit');
                },
                'mKaryawan.mUnit' => function ($query) {
                    $query->select('id', 'nama');
                },
            ])

                ->selectIdx()
                // ->active()
                ->where(function ($query) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::TIDAK_HADIR);
                });

            if ($bawahanFilter === 'saya' && is_null($unit)) {
                $presensi->where('nip', auth()->user()->nip);
            } elseif ($bawahanFilter === 'unit') {
                $presensi->whereHas('mKaryawan', function ($query) use ($user) {
                    $query->where('id_unit', optional($user->mKaryawan)->id_unit);
                });
            } elseif (!is_null($unit)) {
                $presensi->whereHas('mKaryawan', function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                });
            } else {
                $presensi->where('nip', auth()->user()->nip);
            }

            $presensi->when(!is_null($month) && !is_null($year), function ($query) use ($month, $year) {
                $query->whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year);
            })
                ->orderByDesc('tanggal')
                ->orderByDesc('updated_at')
                ->get();

            return $this->okApiResponse(
                $presensi,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function showByNip($nip = null)
    {
        try {
            $date = request('date');

            $presensi = Presensi::selectIdx()->where('nip', $nip)->orderBy('tanggal');

            $presensi->when(!is_null($date), function ($query) use ($date) {
                $query->whereDate('tanggal', $date);
            }, function ($query) {
                $query->whereDate('tanggal', now()->format('Y-m-d'));
            });

            return $this->okApiResponse($presensi->get());
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function progress()
    {
        try {
            $user = auth()->user();
            $unit = is_null(request('unit')) ? $user->mKaryawan->id_unit : request('unit');
            $params = [
                'user' => $user,
                'year' => request('year'),
                'month' => request('month'),
                'unit' => $unit
            ];

            $jadwals = $this->presensiService->presensiUnit($params);

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function grafikKehadiran()
    {
        try {
            $user = auth()->user();
            $unit = is_null(request('unit')) ? $user->mKaryawan->id_unit : request('unit');
            $params = [
                'user' => $user,
                'year' => request('year'),
                'month' => request('month'),
                'unit' => $unit
            ];

            $presensies = $this->presensiService->grafikKehadiran($params);

            $resp = [
                'labels' => $presensies['labels'],
                'series' => $presensies['series'],
            ];

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function searchKaryawan()
    {
        try {
            $unit = request('unit');
            $range = request('range_tanggal');
            $search = request('search');
            $status = request('status');
            $perPage = request('perPage');
            $user = auth()->user();

            $presensi = Jadwal::with([
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
                ->when(!is_null($range), function ($query) use ($range) {
                    $query->where(function ($query) use ($range) {
                        $tgl = explode('to', $range);
                        if (count($tgl) > 1) {
                            $start = Carbon::make($tgl[0])->format('Y-m-d');
                            $end = Carbon::make($tgl[1])->format('Y-m-d');
                            $query->whereBetween('jadwal.tanggal', [$start, $end]);
                        } else {
                            $query->where('jadwal.tanggal', Carbon::make($tgl[0])->format('Y-m-d'));
                        }
                    });
                })
                ->when(!is_null($unit), function ($query) use ($unit) {
                    $query->where('jadwal.id_unit', $unit);
                })
                ->when($user->role === User::STAFF, function ($query) use ($user) {
                    $query->where('jadwal.nip', $user->nip);
                })
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('jadwal.nama', 'LIKE', "%{$search}%")
                        ->orWhere('jadwal.nip', 'LIKE', "%{$search}%");
                })
                ->when(!is_null($status), function ($query) use ($status) {
                    if (in_array($status, ['TELAT', 'TEPAT'])) {
                        $query->whereIn('jadwal.status_absen', ['TELAT', 'TEPAT']);
                    }
                    if (in_array($status, ['TIDAK HADIR'])) {
                        $query->where('jadwal.status', Jadwal::TIDAK_HADIR);
                    }
                    if (in_array($status, ['HADIR'])) {
                        $query->whereNotNull('jadwal.masuk');
                    }
                })
                ->orderByDesc('jadwal.tanggal')
                ->paginate($perPage);

            return $this->okApiResponse($presensi);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function exportHarianExcel()
    {
        try {
            $unit = request('unit');
            $range = request('range_tanggal');
            $search = request('search');
            $status = request('status');
            $user = User::where('nip', request('nip'))->first();

            $data = [
                'unit' => $unit,
                'range' => $range,
                'search' => $search,
                'status' => $status,
                'user' => $user,
            ];

            return Excel::download(new PresensiHarianExport($data), "presensi pada {$range}.xlsx");
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
            // return $this->errorApiResponse($th->getTraceAsString());
        }
    }
}

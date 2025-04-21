<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\JadwalBlue;
use App\Models\JadwalRed;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\MKaryawanDetail;
use App\Models\MShift;
use App\Models\MUnit;
use App\Models\PresensiUser;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    use ApiResponse;

    public function progressPresensi()
    {
        try {

            $progress = PresensiUser::with([
                'jadwal' => function ($query) {
                    $query->select('id', 'tanggal', 'jam_masuk', 'masuk', 'status_absen');
                },
                'mKaryawan' => function ($query) {
                    $query->select('id', 'nip', 'nama', 'photo');
                },
            ])
                ->select('nip', 'tanggal', 'id_jadwal')
                ->limit(24)
                ->orderByDesc('updated_at')
                ->get();

            return $this->okApiResponse($progress, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function lineGrafikAbsen()
    {
        $mulai = now()->subDays(6);
        $akhir = now();
        $periods = CarbonPeriod::create($mulai, $akhir);
        $results = [
            'val' => [
                'name' => '',
                'data' => [],
            ],
            'label' => [],
        ];
        $categories = [
            Jadwal::TIDAK_HADIR,
            Jadwal::PROGRESS,
            Jadwal::SELESAI,
        ];
        foreach ($periods as $periode) {
            $results['label'][] = $periode->format('d-m-Y');
        }
        foreach ($categories as $cat) {
            foreach ($periods as $periode) {
                $data[] = Jadwal::whereDate('tanggal', $periode->format('Y-m-d'))
                    ->where('status', $cat)
                    // ->active()
                    ->count();
            }
            $title = '';
            switch ($cat) {
                case Jadwal::TIDAK_HADIR:
                    $title = 'TIDAK HADIR';
                    break;
                case Jadwal::SELESAI:
                    $title = 'TIDAK HADIR';
                    break;
            }
            $results["val"]["name"] = $title;
            // $results["val"]["data"][] = $ttlPresensi;
        }
        return [
            'mulai' => $mulai,
            'akhir' => $akhir,
            'results' => $results,
        ];
    }

    public function presensiHarian()
    {
        try {

            $start = now()->subDays(6);
            $end = now();
            $user = auth()->user();

            $jadwals = Jadwal::leftJoin('m_unit', 'm_unit.id', '=', 'jadwal.id_unit')
                ->leftJoin('m_karyawan', 'm_karyawan.nip', '=', 'jadwal.nip')
                ->selectRaw('count(*) as ttl_hadir, tanggal')
                ->where(function ($query) use ($start, $end) {
                    $query->where('jadwal.tanggal', '>=', $start)->where('jadwal.tanggal', '<=', $end);
                })
                ->where(function ($query) use ($start, $end) {
                    $query->whereNull('m_karyawan.tgl_resign');
                })
                ->where(function ($query) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::PROGRESS);
                })
                ->when(in_array($user->role, [User::KEPALA, User::STAFF]), function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                })
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get();
            $labels = [];
            $periods = CarbonPeriod::create($start, $end);
            foreach ($periods as $key => $period) {
                if ($key > 0) {
                    $labels[] = $period->format('Y-m-d');
                }
            }

            $resp = [
                'label' => $labels,
                'series' => $jadwals->pluck('ttl_hadir'),
            ];

            return $this->okApiResponse($resp, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function tablePresensiHarian()
    {
        try {
            $now = now();
            $user = auth()->user();
            $shift = request('shift');
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

            $jadwals = Jadwal::leftJoin('m_unit', 'm_unit.id', '=', 'jadwal.id_unit')
                ->leftJoin('m_karyawan', 'm_karyawan.nip', '=', 'jadwal.nip')
                ->select(
                    'jadwal.id',
                    'jadwal.nip',
                    'jadwal.nama',
                    'jadwal.kode_shift',
                    'jadwal.status',
                    'jadwal.libur',
                    'jadwal.id_unit',
                    DB::raw('m_unit.nama as nama_unit'),
                    'm_karyawan.photo'
                )
                ->whereDate('jadwal.tanggal', $now->format('Y-m-d'))
                ->where(function ($query) {
                    $query->whereNull('jadwal.status')
                        ->where('jadwal.libur', 0);
                })
                ->whereNull('tgl_resign')
                ->whereIn('kode_shift', $getShift)
                ->when(in_array($user->role, [User::KEPALA, User::STAFF]), function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                })
                ->when(!is_null(request('nama')), function ($query) {
                    $q = request('nama');
                    $query->where('jadwal.nama', 'LIKE', "%{$q}%");
                })
                ->get();

            return $this->okApiResponse($jadwals, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function tableIzinHarian()
    {
        try {
            $now = now();
            $user = auth()->user();

            $izins = Izin::selectIdx()
                ->whereHas('izinDetail', function ($query) use ($now) {
                    $query->whereDate('tanggal', $now);
                })
                ->when(in_array($user->role, [User::KEPALA, User::STAFF]), function ($query) use ($user) {
                    $query->whereHas('pemohon', function ($query) use ($user) {
                        $query->where('id_unit', $user->mKaryawan->id_unit);
                    });
                })
                ->get();

            return $this->okApiResponse($izins, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function jadwal()
    {
        try {
            $user = auth()->user();

            $jadwals = Jadwal::with([
                'mKaryawan' => function ($query) {
                    $query->select('id', 'nip', 'photo');
                },
            ])
                ->where('nip', $user->nip)
                ->whereDate('tanggal', now()->format('Y-m-d'))
                ->selectIdx()
                ->orderBy('tanggal')
                ->get();

            $lembur = null;

            $results = [
                'jadwal' => $jadwals,
                'lembur' => $lembur,
            ];

            return $this->okApiResponse($results);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function statistikHadir()
    {
        try {
            $hadirTimeFilter = request('hadir');
            DB::beginTransaction();

            $present = DB::table('jadwal')
                ->selectRaw('COUNT(id) as hadir')
                ->where(function ($query) use ($hadirTimeFilter) {
                    $exp = explode('-', $hadirTimeFilter);
                    $query->whereMonth('tanggal', $exp[1])
                        ->whereYear('tanggal', $exp[0]);
                })
                ->where(function ($query) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::PROGRESS);
                })
                ->pluck('hadir')
                ->first();
            $ttl = DB::table('jadwal')
                ->selectRaw('COUNT(id) as ttl')
                ->where(function ($query) use ($hadirTimeFilter) {
                    $exp = explode('-', $hadirTimeFilter);
                    $query->whereMonth('tanggal', $exp[1])
                        ->whereYear('tanggal', $exp[0]);
                })
                ->where('libur', 0)
                ->pluck('ttl')
                ->first();

            $hadirDist = [
                'present' => $present,
                'permission' => DB::table('jadwal')
                    ->selectRaw('COUNT(id) as ttlizin')
                    ->where(function ($query) use ($hadirTimeFilter) {
                        $exp = explode('-', $hadirTimeFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('status', Jadwal::IZIN)
                    ->pluck('ttlizin')
                    ->count(),
                'absent' => DB::table('jadwal')
                    ->selectRaw('COUNT(id) as absent')
                    ->where(function ($query) use ($hadirTimeFilter) {
                        $exp = explode('-', $hadirTimeFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('status', Jadwal::TIDAK_HADIR)
                    ->pluck('absent')
                    ->first(),
                'total_employees' => DB::table('m_karyawan')
                    ->selectRaw('COUNT(id) as ttl')
                    ->where(function ($query) {
                        $query->whereNotNull('tgl_resign')
                            ->where('tgl_resign', '<>', '');
                    })
                    ->pluck('ttl')
                    ->first(),
                'attendance_rate' => $present / $ttl * 100 ?? 0
            ];

            DB::commit();

            return $this->okApiResponse($hadirDist, 'Berhasil memuat data');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function statistikDivisi()
    {
        try {
            $hadirTimeFilter = request('divisi');
            $units = MUnit::join('jadwal', 'jadwal.id_unit', '=', 'm_unit.id')->groupBy('m_unit.nama')->get(['m_unit.id', 'm_unit.nama']);
            $divisi = [
                'unit' => [],
                'present' => [],
                'absent' => [],
                'permission' => [],
            ];
            foreach ($units as $unit) {
                $divisi['unit'][] = $unit->nama;
                $divisi['present'][] = DB::table('jadwal')
                    ->selectRaw('COUNT(id) as ttl')
                    ->where(function ($query) use ($hadirTimeFilter) {
                        $exp = explode('-', $hadirTimeFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('id_unit', $unit->id)
                    ->whereIn('status', [Jadwal::PROGRESS, Jadwal::SELESAI])
                    ->pluck('ttl')
                    ->first();
                $divisi['absent'][] = DB::table('jadwal')
                    ->selectRaw('COUNT(id) as ttl')
                    ->where(function ($query) use ($hadirTimeFilter) {
                        $exp = explode('-', $hadirTimeFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('id_unit', $unit->id)
                    ->where('status', Jadwal::TIDAK_HADIR)
                    ->pluck('ttl')
                    ->first();
                $divisi['permission'][] = DB::table('jadwal')
                    ->selectRaw('COUNT(id) as ttl')
                    ->where(function ($query) use ($hadirTimeFilter) {
                        $exp = explode('-', $hadirTimeFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('id_unit', $unit->id)
                    ->where('status', Jadwal::IZIN)
                    ->pluck('ttl')
                    ->first();
            }

            return $this->okApiResponse($divisi, 'Berhasil memuat data');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function statistikDistr()
    {
        try {
            $distrFilter = request('distr');

            DB::beginTransaction();

            $present = DB::table('jadwal')
                ->selectRaw('COUNT(id) as hadir')
                ->where(function ($query) use ($distrFilter) {
                    $exp = explode('-', $distrFilter);
                    $query->whereMonth('tanggal', $exp[1])
                        ->whereYear('tanggal', $exp[0]);
                })
                ->where(function ($query) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::PROGRESS);
                })
                ->pluck('hadir')
                ->first();

            $distr = [
                'present' => $present,
                'permission' => DB::table('jadwal')
                    ->selectRaw('COUNT(id) as ttlizin')
                    ->where(function ($query) use ($distrFilter) {
                        $exp = explode('-', $distrFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('status', Jadwal::IZIN)
                    ->pluck('ttlizin')
                    ->count(),
                'absent' => DB::table('jadwal')
                    ->selectRaw('COUNT(id) as absent')
                    ->where(function ($query) use ($distrFilter) {
                        $exp = explode('-', $distrFilter);
                        $query->whereMonth('tanggal', $exp[1])
                            ->whereYear('tanggal', $exp[0]);
                    })
                    ->where('status', Jadwal::TIDAK_HADIR)
                    ->pluck('absent')
                    ->first(),
            ];

            DB::commit();

            return $this->okApiResponse($distr, 'Berhasil memuat data');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function statistikTrend()
    {
        try {
            $trend = [];
            $start = now()->subDays(7);
            $end = now();
            $trend['present'] = DB::table('jadwal')
                ->selectRaw('COUNT(id) as ttl, tanggal')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('tanggal', [$start, $end]);
                })
                ->where(function ($query) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::PROGRESS);
                })
                ->groupBy('tanggal')
                ->get();
            $trend['permission'] = DB::table('jadwal')
                ->selectRaw('COUNT(id) as ttl, tanggal')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('tanggal', [$start, $end]);
                })
                ->where(function ($query) {
                    $query->where('status', Jadwal::IZIN);
                })
                ->groupBy('tanggal')
                ->get();
            $trend['absent'] = DB::table('jadwal')
                ->selectRaw('COUNT(id) as ttl, tanggal')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('tanggal', [$start, $end]);
                })
                ->where(function ($query) {
                    $query->where('status', Jadwal::TIDAK_HADIR);
                })
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get();

            $dates = CarbonPeriod::create($start, '1 day', $end);

            $trend['date'] = [];

            foreach ($dates as $date) {
                $trend['date'][] = $date->format('d/m');
            }

            return $this->okApiResponse($trend, 'Berhasil memuat data');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function statistikGender()
    {
        try {
            $gender = [];
            $gender['l'] = MKaryawan::where('sex', 'L')->notResign()->count();
            $gender['p'] = MKaryawan::where('sex', 'P')->notResign()->count();

            return $this->okApiResponse($gender, 'Berhasil memuat data');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function listPresensi()
    {
        try {
            $jenis = request('jenis');
            $dateMonth = request('month');
            $jadwal = Jadwal::selectRaw('COUNT(jadwal.id) as ttl, jadwal.nama, jadwal.unit')
                ->leftJoin('m_karyawan', 'm_karyawan.nip', '=', 'jadwal.nip')
                ->when($jenis === 'terendah', function ($query) {
                    $query->where('jadwal.status', 3);
                })
                ->when(!is_null($dateMonth), function ($query) use ($dateMonth) {
                    $exp = explode('-', $dateMonth);
                    $query->where(function ($query) use ($exp) {
                        $query->whereMonth('jadwal.tanggal', $exp[1])
                            ->whereYear('jadwal.tanggal', $exp[0]);
                    });
                })
                ->groupBy('jadwal.nama')
                ->limit(10)
                ->orderByDesc('ttl')
                ->get();

            return $this->okApiResponse($jadwal, 'Berhasil memuat data');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

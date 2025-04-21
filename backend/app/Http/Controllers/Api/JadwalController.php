<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreJadwalRequest;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\MKaryawan;
use App\Models\PresensiUser;
use App\Models\User;
use App\Services\JadwalService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{

    use ApiResponse;

    private JadwalService $jadwalService;

    public function __construct(JadwalService $jadwalService)
    {
        $this->jadwalService = $jadwalService;
    }

    public function index()
    {
        try {
            $unit = request('unit');
            $user = auth()->user();

            $karyawans = MKaryawan::notResign()
                ->select([
                    'm_karyawan.nip',
                    'm_karyawan.nama',
                    'm_karyawan.tgl_lahir',
                    'm_karyawan.jabatan',
                    'm_karyawan.pendidikan',
                    'm_karyawan.unit',
                    'm_karyawan.photo',
                ])
                ->when(
                    $user->role === User::STAFF && is_null($unit),
                    function ($query) use ($user) {
                        $query->where('m_karyawan.id_unit', $user->mKaryawan->id_unit);
                    }
                )
                ->when(
                    !is_null($unit),
                    function ($query) use ($unit) {
                        $query->where('m_karyawan.id_unit', $unit);
                    }
                )
                ->get();

            return $this->okApiResponse(
                $karyawans,
                (string) $unit
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreJadwalRequest $request)
    {

        try {
            $params = [
                "request" => $request->validated(),
                "user" => auth()->user(),
            ];

            $jadwal = null;
            if ($request->type_tanggal === 'harian') {
                $jadwalExists = Jadwal::where([
                    'tanggal' => $request->tanggal,
                    'nip' => $request->nip,
                    'kode_shift' => $request->kode_shift
                ])->count();

                if ($jadwalExists > 0) {
                    throw new Exception('Jadwal sudah ada !');
                }

                $jadwal = $this->jadwalService->store($params);
            } else if ($request->type_tanggal === 'range') {
                $jadwal = $this->jadwalService->storeWithRange($params);
            }

            return $this->okApiResponse($jadwal, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function storeWithRange(Request $request)
    {

        try {

            $params = [
                "request" => $request->all(),
                "user" => auth()->user(),
            ];

            $resp = $this->jadwalService->storeWithRange($params);

            return $this->okApiResponse($resp, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $jadwal = Jadwal::with([
                'mKaryawan' => function ($query) {
                    $query->select('id', 'nip', 'photo');
                },
                'presensi',
                'presensiTerlambat'
            ])
                ->selectIdx()
                ->where('id', $id)
                ->first();

            return $this->okApiResponse($jadwal, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $params = [
                "request" => $request->all(),
                "user" => auth()->user()
            ];

            if (request('column') === 'shift') {
                $jadwal = $this->jadwalService->update($params, $id);
            } else if (request('column') === 'libur') {
                $jadwal = $this->jadwalService->updateLibur($params, $id);
            }

            return $this->okApiResponse($jadwal, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updatePresensi(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $jadwal = Jadwal::findOrFail($id);
            if (now()->lessThan(Carbon::make($jadwal->tanggal)->format('Y-m-d'))) {
                throw new Exception('Anda Belum waktunya absen');
            }
            $jadwal->status = $request->status;
            $jadwal->masuk = $request->masuk;
            $jadwal->status_absen = $request->status_absen;
            $sessionPresensi = PresensiUser::where('id_jadwal', $jadwal->id)->first();
            if ($request->status === Jadwal::PROGRESS) {
                if (is_null($sessionPresensi)) {
                    PresensiUser::create([
                        'id_jadwal' => $jadwal->id,
                        'nip' => $jadwal->nip,
                        'tanggal' => $jadwal->tanggal,
                    ]);
                }
            } elseif ($request->status === Jadwal::SELESAI) {
                $jadwal->tgl_pulang = $request->tgl_pulang;
                $jadwal->pulang = $request->pulang;
                $jadwal->ttlkerja = $request->ttlkerja;
                $jadwal->ttltelat = $request->ttltelat;
                $jadwal->auto = $request->auto;
                if (!is_null($sessionPresensi)) {
                    $sessionPresensi->delete();
                }
            }

            $jadwal->save();

            DB::commit();

            return $this->okApiResponse($jadwal, 'Berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $jadwal = Jadwal::find($id);
            $jadwal->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroyAll(Request $request)
    {
        try {

            Jadwal::whereIn("id", $request->id)->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function showByNip($nip = null)
    {
        try {
            $month = request('month');
            $year = request('year');
            $jadwals = Jadwal::where('nip', $nip)
                ->selectIdx()
                ->orderBy('tanggal')
                ->when(!is_null($month), function ($query) use ($month) {
                    $query->whereMonth('tanggal', $month);
                })
                ->when(!is_null($year), function ($query) use ($year) {
                    $query->whereYear('tanggal', $year);
                })
                ->latest()
                ->get();

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
    public function jadwalku()
    {
        try {
            $user = auth()->user();
            $year = request('year');
            $month = request('month');
            $day = request('day');
            $date = Carbon::createFromFormat('Y-m', "$year-$month");

            $jadwals = Jadwal::leftJoin('presensi', 'presensi.id_jadwal', '=', 'jadwal.id')
                ->leftJoin('presensi_terlambat', 'presensi_terlambat.id_jadwal', '=', 'jadwal.id')
                ->leftJoin('izin_detail', 'izin_detail.tanggal', '=', 'jadwal.tanggal')
                ->leftJoin('izin', 'izin_detail.id_izin', '=', 'izin.id')
                ->where('jadwal.nip', $user->nip)
                ->when(!is_null($day), function ($query) use ($date) {
                    $query->whereDate('jadwal.tanggal', $date);
                })
                ->when(is_null($day), function ($query) use ($date) {
                    $query->whereMonth('jadwal.tanggal', $date->format('m'))
                        ->whereYear('jadwal.tanggal', $date->format('Y'));
                })
                ->orderBy('jadwal.tanggal')
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
                ->get();

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function jadwalkuHarian()
    {
        try {
            $user = auth()->user();

            $jadwals = Jadwal::with([
                'presensi' => function ($query) {
                    $query->select('id_jadwal', 'status', 'masuk', 'pulang');
                }
            ])
                ->where([
                    'nip' => $user->nip,
                    'tanggal' => now()->format('Y-m-d'),
                    'libur' => 0
                ])
                ->select([
                    'id',
                    'tanggal',
                    'nip',
                    'id_unit',
                    'nama',
                    'kode_shift',
                    'shift',
                    'mulai_absen',
                    'jam_masuk',
                    'telat_masuk',
                    'jam_pulang',
                    'telat_pulang',
                    'status',
                    'libur',
                ])
                ->orderBy('tanggal')
                ->get();

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateJadwalku(Request $request, $id)
    {
        try {
            $params = [
                'request' => $request->all(),
                'user' => auth()->user(),
            ];
            $jadwal = $this->jadwalService->update($params, $id);

            return $this->okApiResponse($jadwal);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function import(Request $request)
    {
        try {

            $file = $request->file('file');
            $params = [
                'file' => $file,
                'year' => request('year'),
                'month' => request('month'),
            ];

            $date = Carbon::createFromFormat('Y-m', "{$params['year']}-{$params['month']}");
            if (
                $date->lessThan(now()) && !$date->isSameMonth(now())

            ) {
                throw new Exception('Tahun dan Bulan Tidak rendah dari ' . now()->isoFormat('MMMM Y'));
            }

            $this->jadwalService->importExcel($params);

            return $this->okApiResponse('OK');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            $params = [
                'year' => request('year'),
                'month' => request('month'),
                'idUnit' => request('idUnit'),
            ];

            return $this->jadwalService->download($params);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function jadwalUnit()
    {
        try {
            $user = auth()->user();
            $unit = is_null(request('idUnit')) || request('idUnit') < 1 ? $user->mKaryawan->id_unit : request('idUnit');
            $params = [
                'user' => $user,
                'year' => request('year'),
                'month' => request('month'),
                'unit' => $unit
            ];

            $jadwals = $this->jadwalService->jadwalUnit($params);

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function jadwalUnitStatus()
    {
        try {
            $unit = request('unit');
            $data = [];

            $data['approved_by_count'] = Jadwal::query()
                ->where(function ($query) {
                    $query->whereMonth('tanggal', request('month'))->whereYear('tanggal', request('year'));
                })
                ->where(function ($query) {
                    $query->where('validate_by', auth()->user()->nip);
                })
                ->whereHas('mKaryawan', function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->count();

            $data['approved_at_count'] = Jadwal::query()
                ->where(function ($query) {
                    $query->whereMonth('tanggal', request('month'))->whereYear('tanggal', request('year'));
                })
                ->where(function ($query) {
                    $query->whereNull('validate_at')->where('validate_by', auth()->user()->nip);
                })
                ->whereHas('mKaryawan', function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->count();

            return $this->okApiResponse($data);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function approval(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $listNip = $request->list_nip;
            $month = $request->month;
            $year = $request->year;

            $jadwals = Jadwal::whereIn('nip', $listNip)
                ->where(function ($query) use ($month, $year) {
                    $query->whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year);
                })
                ->where(function ($query) use ($user) {
                    $query->where('validate_by', $user->nip)
                        ->whereNull('validate_at');
                })
                ->notActive()
                ->get();

            if ($jadwals->isEmpty()) {
                throw new Exception('Anda bukan approval dari jadwal');
            }

            foreach ($jadwals as $item) {
                $item->update([
                    'validate_at' => now()
                ]);
            }
            DB::commit();

            return $this->okApiResponse('OK');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function approvalInfo()
    {
        try {
            $user = auth()->user();
            if ($user->role !== User::SUPER_ADMIN) {
                $data = Jadwal::join('jadwal.nip', '=', 'm_karyawan.')
                    ->select('')
                    ->where('validate_by', $user->nip)
                    ->groupBy('');
            }

            return $this->okApiResponse($data);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function checkPeriode()
    {
        try {

            $periode = request('periode');
            $mulai = request('mulai');
            $nip = request('nip');
            $tahunan = request('tahunan');

            $jadwals = Jadwal::where('nip', $nip)
                ->whereBetween(
                    'tanggal',
                    [
                        Carbon::createFromFormat('Y-m-d', $mulai)->format('Y-m-d'),
                        Carbon::createFromFormat('Y-m-d', $mulai)
                            ->addDays($periode - 1)
                            ->format('Y-m-d'),
                    ]
                )
                ->orderByDesc('tanggal')
                ->get([
                    'id',
                    'nip',
                    'tanggal',
                    'libur',
                ]);

            if ($jadwals->isEmpty()) {
                $mulai = Carbon::createFromFormat('Y-m-d', $mulai)->format('d-m-Y');
                throw new Exception("Anda Tidak Punya Jadwal Tanggal {$mulai}");
            }

            $jadwal = $jadwals->first();

            // get tanggal akhir yang tidak libur
            $tglAkhir = (new JadwalService())->getTanggalMasukRecursive(
                Carbon::createFromFormat('Y-m-d', $jadwal->tanggal),
                $jadwal->nip
            );

            // get tanggal masuk yang tidak libur
            $tglMasuk = (new JadwalService())->getTanggalMasukRecursive(
                Carbon::createFromFormat('Y-m-d', $tglAkhir)->addDays(1),
                $jadwal->nip
            );

            $sisaCuti = null;
            if ($tahunan) {
                $sisaCuti = MKaryawan::where('nip', $jadwal->nip)->pluck('jml_cuti')->first() - $periode;
            }

            $resp = [
                'tgl_akhir' => $tglAkhir,
                'tgl_masuk' => $tglMasuk,
                'sisa_cuti' => $sisaCuti,
            ];

            return $this->okApiResponse(
                $resp,
                'Berhasil memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function kosongkanJadwal(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $month = request('month');
            $year = request('year');
            if (now()->greaterThan(\Carbon\Carbon::make("{$year}-{$month}"))) {
                throw new Exception('Tidak bisa menghapus bulan sebelumnya');
            }

            $jadwals = Jadwal::whereIn('nip', $request->list_nip)
                ->where(function ($query) use ($month, $year) {
                    $query->whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year);
                })
                // ->whereNull('validate_at')
                ->whereNull('status')
                ->when($user->role !== User::SUPER_ADMIN, function ($query) {
                    $query->where(function ($query) {
                        $query->whereNotNull('validate_by')
                            ->whereNull('validate_at');
                    });
                });

            if ($jadwals->get()->isEmpty()) {
                throw new Exception('Jadwal Kosong/Sudah divalidasi. Silahkan konfirmasi SDM');
            }

            $jadwals->delete();

            DB::commit();

            return $this->okApiResponse('OK');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function notif()
    {
        try {

            $user = auth()->user();

            $jadwals = Jadwal::whereBetween('tanggal', [
                now()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ])
                ->where('nip', $user->nip)
                ->whereNull('status')
                ->orderBy('tanggal')
                ->limit(1)
                ->get();

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function showByDate($date)
    {
        try {

            $user = auth()->user();

            $jadwals = Jadwal::with([
                'presensiTerlambat',
                'izin' => function ($query) {
                    $query->where('acc_status', Izin::SELESAI);
                },
            ])
                ->whereDate('tanggal', Carbon::make($date)->format('Y-m-d'))
                ->where('nip', $user->nip)
                ->get();

            return $this->okApiResponse($jadwals, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

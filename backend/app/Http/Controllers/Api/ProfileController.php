<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\MKaryawan;
use App\Models\Presensi;
use App\Models\TrainKaryawan;
use App\Models\TukarJadwal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ApiResponse;

    public function myProfil()
    {
        try {
            $user = auth()->user()->load('mKaryawan');
            $karyawan = $user->mKaryawan;

            return $this->okApiResponse($karyawan, 'Berhasil dimuat');
        } catch (\Throwable $th) {
            return $this->okApiResponse($th->getMessage());
        }
    }

    public function show($nip)
    {
        try {
            $karyawan = MKaryawan::where('nip', $nip)->first();

            return $this->okApiResponse($karyawan, 'Berhasil dimuat');
        } catch (\Throwable $th) {
            return $this->okApiResponse($th->getMessage());
        }
    }

    public function jadwal($nip)
    {
        try {
            $month = request('month');
            $year = request('year');

            $jadwals = Jadwal::with([
                'mShift' => function ($query) {
                    $query->select('kode', 'color');
                },
                'izinDetail' => function ($query) {
                    $query->select('id_izin', 'tanggal', 'nip');
                },
                'izinDetail.izin' => function ($query) {
                    $query->select('id', 'izin', 'kode_izin');
                },
                'izinForKrs' => function ($query) {
                    $query->select('id', 'izin', 'kode_izin', 'tanggal');
                },
                'tukarJadwal' => function ($query) {
                    $query->select('id', 'tanggal', 'nip_pihak1');
                },
            ])
                ->selectIdx()
                ->where([
                    'nip' => $nip,
                ])
                ->when(!is_null($month) && !is_null($year), function ($query) use ($month, $year) {
                    $query->whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year);
                }, function ($query) {
                    $query->whereMonth('tanggal', now()->format('m'))
                        ->whereYear('tanggal', now()->format('Y'));
                })
                ->get();

            return $this->okApiResponse($jadwals);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getEvent()
    {
        try {
            $listType = ["jadwal", "izin"];
            if (!in_array(request("type"), $listType)) {
                throw new Exception("Jenis tidak sesuai");
            }

            $resp = collect([]);
            switch (request("type")) {
                case 'jadwal':
                    $resp = Jadwal::with('presensi')->find(request('id'));
                    break;
                case 'izin':
                    $resp = Izin::find(request('id'), ['ket']);
                    break;
            }
            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function gantiProfil(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:jpg,jpeg,png,bmp,tiff |max:2048',
            ], [
                'mimes' => 'Upload berupa gambar',
                'max'   => 'Maksimal 3 MB'
            ]);
            if ($validator->fails()) {
                throw new Exception('Upload berupa gambar dan Maksimal 2 MB');
            }
            $user = auth()->user();
            $file = $request->file('file');
            /**
             * RUN UPLOAD
             */
            $directory = Storage::disk('public')->path('profil');

            if (File::ensureDirectoryExists($directory)) {
                $directory = File::makeDirectory($directory);
            }

            $karyawan = $user->mKaryawan;
            if (!is_null($karyawan->photo)) {
                unlink($directory . '/' . $karyawan->photo);
            }

            $filename = $user->nip . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profil', $filename, 'public');
            $karyawan->photo = $filename;
            $karyawan->save();

            return $this->okApiResponse('OK');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getTraceAsString());
        }
    }

    public function getProfil($nip)
    {
        $karyawan = MKaryawan::where('nip', $nip)->first(['photo']);
        if (Storage::disk('public')->exists('profil/' . $karyawan->photo)) {
            return response()->file(Storage::disk('public')->path('profil/' . $karyawan->photo));
        } else {
            return response()->json(false);
        }
    }

    public function getPelatihan($nip)
    {
        try {
            $pelatihan = TrainKaryawan::with('train')->where('nip', $nip)->get();

            return $this->okApiResponse($pelatihan);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getIzin($nip)
    {
        try {
            $range = request('range');

            $izins = Izin::selectIdx()
                ->where('nip', $nip)
                ->when(!is_null($range), function ($query) use ($range) {
                    $split = explode('to', $range);
                    $query->whereBetween('mulai', [
                        Carbon::make($split[0])->format('Y-m-d'),
                        Carbon::make($split[1])->format('Y-m-d')
                    ]);
                }, function ($query) {
                    $query->whereMonth('mulai', now()->format('m'))->whereYear('mulai', now()->format('Y'));
                })
                ->latest()
                ->get();

            $resp = [
                'list' => $izins,
                'statistik' => $izins->groupBy('izin')->map->count(),
                'sisa_cuti' => DB::table('m_karyawan')->where('nip', $nip)->pluck('jml_cuti')
            ];

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getTukar($nip)
    {
        try {
            $range = request('range');

            $tukar = TukarJadwal::where('nip_pihak1', $nip)
                ->when(!is_null($range), function ($query) use ($range) {
                    $split = explode('to', $range);
                    $query->whereBetween('tgl_pihak1', [
                        Carbon::make($split[0])->format('Y-m-d'),
                        Carbon::make($split[1])->format('Y-m-d')
                    ]);
                }, function ($query) {
                    $query->whereMonth('tanggal', now()->format('m'))->whereYear('tanggal', now()->format('Y'));
                })
                ->latest()
                ->get(['id', 'jenis', 'tgl_pihak1', 'tgl_pihak2', 'nama_pihak2']);

            $resp = [
                'list' => $tukar,
                'statistik' => $tukar->groupBy('jenis_desc')->map->count(),
            ];

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getPresensi($nip)
    {
        try {
            $range = request('range');

            $presensi = Jadwal::leftJoin('presensi', 'presensi.id_jadwal', '=', 'jadwal.id')
                ->where('jadwal.nip', $nip)
                ->when(!is_null($range), function ($query) use ($range) {
                    $split = explode('to', $range);
                    $query->whereBetween('jadwal.tanggal', [
                        Carbon::make($split[0])->format('Y-m-d'),
                        Carbon::make($split[1])->format('Y-m-d')
                    ]);
                }, function ($query) {
                    $query->whereMonth('jadwal.tanggal', now()->format('m'))->whereYear('jadwal.tanggal', now()->format('Y'));
                })
                ->orderByDesc('jadwal.created_by')
                ->get([
                    'jadwal.id',
                    'jadwal.tanggal',
                    'jadwal.shift',
                    'jadwal.masuk',
                    'jadwal.pulang',
                    'jadwal.status',
                    DB::raw('jadwal.status_absen as presensi_status')
                ]);

            $resp = [
                'list' => $presensi,
                'statistik' => $presensi->groupBy('status_cast')->map->count(),
            ];

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function fcmUpdate(Request $request)
    {
        $user = auth()->user();
        if ($user->currentAccessToken()->fcm_token !== $request->token) {
            $currentToken = $user->currentAccessToken();
            $currentToken->update([
                'fcm_token' => $request->token
            ]);
            // $user->currentAccessToken()->fcm_token = $request->token;
            // $user->currentAccessToken()->save();
        }

        return $this->okApiResponse($user->currentAccessToken()->fcm_token);
    }

    public function statistikPresensi($nip)
    {
        try {

            $karyawan = Jadwal::selectRaw('COUNT(id) as ttl_presensi, MONTH(tanggal) as jadwal_month')
                ->where(function ($query) use ($nip) {
                    $query->where('nip', $nip)
                        ->whereIn('status', [Jadwal::SELESAI, Jadwal::PROGRESS]);
                })
                ->whereBetween('tanggal', [now()->subMonths(6), now()])
                ->groupBy('jadwal_month')
                ->get();

            $izin = Izin::selectRaw('COUNT(id) as ttl_izin, MONTH(tanggal) as jadwal_month')
                ->where(function ($query) use ($nip) {
                    $query->where('nip', $nip)
                        ->where('acc_status', Izin::SELESAI);
                })
                ->whereBetween('tanggal', [now()->subMonths(6), now()])
                ->groupBy('jadwal_month')
                ->get();

            $months = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Augustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $resp = [
                'label' => $karyawan->map(function ($item) use ($months) {
                    return $months[$item->jadwal_month];
                }),
                'series' => [
                    'presensi' => $karyawan->pluck('ttl_presensi'),
                    'izin' => $izin->pluck('ttl_izin'),
                ],
            ];

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getTraceAsString());
        }
    }

    public function grafikStatistik($nip)
    {
        try {
            $date = now()->subMonths(1);
            $resp = [
                "year" => $date->format("Y"),
                "month" => $date->format("m"),
                "list" => [],
                "tepat" => [
                    "val" => 0,
                    "label" => "Hari",
                ],
                "telat" => [
                    "val" => 0,
                    "label" => "Hari",
                ],
                "alpa" => [
                    "val" => 0,
                    "label" => "Hari",
                ],
                "izin" => [
                    "val" => 0,
                    "label" => "Hari",
                    "color" => "#64B5F6"
                ],
                "tukar shift" => [
                    "val" => 0,
                    "label" => "Hari",
                    "color" => "#BA68C8"
                ],
            ];

            $tepatCount = Presensi::where(function ($query) use ($nip) {
                $query->where('status', Presensi::TEPAT)->where('nip', $nip);
            })->where(function ($query) use ($date) {
                $query->whereMonth('tanggal', $date->format('m'))
                    ->whereYear('tanggal', $date->format('Y'));
            })->count();

            $telatCount = Presensi::where(function ($query) use ($nip) {
                $query->where('status', Presensi::TELAT)->where('nip', $nip);
            })->where(function ($query) use ($date) {
                $query->whereMonth('tanggal', $date->format('m'))
                    ->whereYear('tanggal', $date->format('Y'));
            })->count();

            $alpaCount = Jadwal::where(function ($query) use ($nip) {
                $query->where('status', Jadwal::TIDAK_HADIR)
                    ->where('libur', 0)
                    ->where('nip', $nip);
            })->where(function ($query) use ($date) {
                $query->whereMonth('tanggal', $date->format('m'))
                    ->whereYear('tanggal', $date->format('Y'));
            })->count();

            $jadwalProgress = Jadwal::where(function ($query) use ($nip) {
                $query->where('nip', $nip)->where('libur', 0);
            })->where(function ($query) use ($date) {
                $query->whereMonth('tanggal', $date->format('m'))
                    ->whereYear('tanggal', $date->format('Y'));
            })->whereNotNull('status')
                ->count();

            $jadwalCount = Jadwal::where(function ($query) use ($nip) {
                $query->where('nip', $nip)
                    ->where('libur', 0);
            })->where(function ($query) use ($date) {
                $query->whereMonth('tanggal', $date->format('m'))
                    ->whereYear('tanggal', $date->format('Y'));
            })->count();

            $resp['list'] += [
                "tepat" => [
                    "val" => $tepatCount,
                    "label" => "Hadir",
                    "color" => "#C8E6C9",
                    "percent" => $jadwalCount > 0 ? round((int) $tepatCount / (int) $jadwalCount * 100) : 0
                ],
                "telat" => [
                    "val" => $telatCount,
                    "label" => "Telat",
                    "color" => "#FFE0B2",
                    "percent" => $jadwalCount > 0 ? round((int) $telatCount / (int) $jadwalCount * 100) : 0
                ],
                "alpa" => [
                    "val" => $alpaCount,
                    "label" => "Alpa",
                    "color" => "#BBDEFB",
                    "percent" => $jadwalCount > 0 ? round((int) $alpaCount / (int) $jadwalCount * 100) : 0
                ],
                "Jadwal" => [
                    "val" => $jadwalCount,
                    "label" => "Jadwal",
                    "color" => "#F0F4C3",
                    "percent" => $jadwalCount > 0 ? intval((int) $jadwalProgress / (int) $jadwalCount * 100) : 0
                ],
            ];

            return $this->okApiResponse($resp, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateAvatar(Request $request, $nip)
    {
        try {

            $profil = $request->file('photo');
            $karyawan = MKaryawan::where('nip', $nip)->firstOrFail(['id', 'nip', 'photo']);
            if (!is_null($karyawan->photo)) {
                if (Storage::disk('public')->exists('profil/' . $karyawan->photo)) {
                    Storage::disk('public')->delete('profil/' . $karyawan->photo);
                }
            }
            Log::info('info profil', [$profil->getFileInfo()]);
            $filename = $karyawan->nip . '.' . $profil->getClientOriginalExtension();
            $profil->storeAs('profil', $filename, 'public');
            $karyawan->photo = $filename;
            $karyawan->save();

            return $this->okApiResponse($filename, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

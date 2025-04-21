<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\MKaryawan;
use App\Models\MLokasi;
use App\Models\Presensi;
use App\Models\PresensiTerlambat;
use App\Models\PresensiUser;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PresensiService
{
    private $presensiUserService;

    public function __construct()
    {
        $this->presensiUserService = new PresensiUserService();
    }

    public function index(array $params)
    {
        $presensies = Presensi::where('nip', $params['user']->nip);
        $filterStatus = $params['filter']['status'];
        $filterBln = $params['filter']['bulan'];
        $filterThn = $params['filter']['tahun'];
        /**
         * FILTER STATUS
         */
        $presensies->when(!is_null($filterStatus), function ($query) use ($filterStatus) {
            $query->where('status', $filterStatus);
        });

        /**
         * FILTER BULAN
         */
        $presensies->when(!is_null($filterBln), function ($query) use ($filterBln) {
            $query->whereMonth('tanggal', $filterBln);
        });

        /**
         * FILTER TAHUN
         */
        $presensies->when(!is_null($filterThn), function ($query) use ($filterThn) {
            $query->whereYear('tanggal', $filterThn);
        });

        return $presensies->get();
    }

    public function masuk(array $params)
    {
        $now = $params['dateNow'];
        $input = $params['request'];

        try {

            DB::beginTransaction();
            $jadwal = Jadwal::find($input['id_jadwal']);

            $mulai = Carbon::createFromFormat('Y-m-d H:i', "{$jadwal->tanggal} {$jadwal->jam_masuk}");
            $pulang = Carbon::createFromFormat('Y-m-d H:i',  "{$jadwal->tanggal} {$jadwal->jam_pulang}");
            $mulai->subMinutes(intval($jadwal->mulai_absen));

            /**
             *  CEK PRESENSI SEBELUM JADWAL
             */
            if ($now->lessThan($mulai)) {
                throw new Exception("Absen dapat dilakukan {$jadwal->mulai_absen} sebelum jadwal");
            } else if ($now->greaterThan($pulang)) {
                throw new Exception("Gagal! Absen dapat dilakukan pada {$jadwal->jam_masuk} - {$jadwal->jam_pulang}");
            }

            $presensiPayload = $input['presensi'];
            $presensiPayload['ip'] = request()->ip();
            $presensiPayload['latlng_masuk'] = $input['latlng_masuk'];
            $presensiPayload['lok_masuk'] = $input['lok_masuk']['nama'];
            Presensi::create($presensiPayload);

            $telat = Carbon::createFromFormat('Y-m-d H:i', "{$jadwal->tanggal} {$jadwal->jam_masuk}")
                ->addMinutes(intval($jadwal->telat_masuk) + 1);
            /**
             *  UPDATE PRESENSI JADWAL
             */
            $jadwal->tgl_masuk = $now->format('Y-m-d');
            $jadwal->masuk = $now->format('H:i');

            /**
             * CEK KETERLAMBATAN (LEBIH)
             */
            if ($now->greaterThan($telat->format('Y-m-d H:i'))) {
                $payload = [
                    'id_jadwal' => $input['id_jadwal'],
                    'jenis' => Jadwal::PRESEN_TELAT,
                    'ket' => '-',
                ];
                $jadwal->ttltelat = $now->diffInMinutes($telat);
                $jadwal->status_absen = Jadwal::PRESEN_TELAT;

                PresensiTerlambat::create($payload);
            } else {

                $jadwal->status_absen = Jadwal::PRESEN_TEPAT;
            }

            $jadwal->status = Jadwal::PROGRESS;
            $jadwal->save();

            PresensiUser::create([
                'id_jadwal' => $jadwal->id,
                'nip' => $jadwal->nip,
                'tanggal' => $jadwal->tanggal,
            ]);

            DB::commit();

            return $jadwal;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function pulang(array $params)
    {
        try {
            DB::beginTransaction();

            $now = $params['dateNow'];
            $input = $params['request'];
            $jadwal = Jadwal::find($input['id_jadwal']);


            $beforePulang = Carbon::createFromFormat('Y-m-d H:i',  "{$jadwal->tanggal}{$jadwal->jam_pulang}");

            if (is_null($jadwal->masuk)) {
                throw new Exception("Belum presensi masuk");
            }

            /**
             *  CEK PRESENSI SEBELUM JADWAL
             */
            $masuk = Carbon::createFromFormat('Y-m-d H:i',  "{$jadwal->tanggal} {$jadwal->jam_masuk}");
            if ($masuk->greaterThan($beforePulang)) {
                $beforePulang->addDay();
            }

            if ($now->lessThan($beforePulang)) {
                throw new Exception("Absen pulang dilakukan {$beforePulang->format('d-m-Y H:i')}");
            }

            $telat = Carbon::createFromFormat('Y-m-d H:i', $jadwal->tanggal . " {$jadwal->jam_pulang}")
                ->addMinutes($jadwal->telat_pulang);

            /**
             * CEK KETERLAMBATAN
             */
            $ttlKerja = 0;
            if ($now->greaterThan($telat)) {
                $ttlKerja = $masuk->diffInMinutes($beforePulang);

                $payload = [
                    'id_jadwal' => $jadwal->id,
                    'jenis' => 'PULANG',
                ];

                PresensiTerlambat::create($payload);
            } else {
                $ttlKerja = $masuk->diffInMinutes($now);
            }

            $jadwal->tgl_pulang = $now->format('Y-m-d');
            $jadwal->pulang = $now->format('H:i');

            $jadwal->status = Jadwal::SELESAI;
            $jadwal->ttlkerja = $ttlKerja;
            $jadwal->save();

            $presensi = Presensi::where('id_jadwal', $jadwal->id)->first();
            $presensi->update([
                'latlng_pulang' => $input['latlng_pulang'],
                'lok_pulang' => $input['lok_pulang']['nama']

            ]);

            $sessionPresensi = PresensiUser::where('id_jadwal', $jadwal->id)->first();
            if (!is_null($sessionPresensi)) {
                $sessionPresensi->delete();
            }

            DB::commit();

            return $jadwal;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function checkAbsenMasuk($user)
    {
        $getPresensi = Jadwal::with([
            'presensi' =>  function ($query) {
                $query->select('id', 'id_jadwal', 'masuk', 'pulang', 'status');
            },
            'presensi.presensiTerlambat' =>  function ($query) {
                $query->select([
                    'id',
                    'id_presensi',
                    'jenis',
                    'ket',
                ]);
            },
        ])
            ->where(['nip' => $user->nip, 'tanggal' => now()->format('Y-m-d'), 'libur' => 0])
            ->where(function ($query) {
                $query->where('status', Jadwal::PROGRESS);
            })
            ->active()
            ->first([
                'id',
                'nip',
                'tanggal',
                'kode_shift',
                'jam_masuk',
                'jam_pulang',
                'status',
                'libur',
            ]);

        return $getPresensi;
    }

    public function getJadwal($nip)
    {
        $sessionPresensi = PresensiUser::where('nip', $nip)->first();
        $params = [
            'type' => 'date',
            'val' => now()->format('Y-m-d'),
        ];

        if (!is_null($sessionPresensi)) {
            $params = [
                'type' => 'id',
                'val' => $sessionPresensi->id_jadwal
            ];
        }

        $presensiUser = Jadwal::leftJoin('presensi', 'jadwal.id', '=', 'presensi.id')
            ->leftJoin('izin_detail', 'jadwal.tanggal', '=', 'izin_detail.id')
            ->leftJoin('izin', 'izin_detail.id_izin', '=', 'izin.id')
            ->leftJoin('presensi_terlambat', 'jadwal.id', '=', 'presensi_terlambat.id_jadwal')
            ->select(
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
                'jadwal.ttlkerja',
                'izin.kode_izin',
                'presensi_terlambat.ket',
            )
            ->where('jadwal.nip', $nip)
            ->when($params['type'] === 'id', function ($query) use ($params) {
                $query->where('jadwal.id', $params['val']);
            }, function ($query) use ($params, $nip) {
                $query->where('jadwal.tanggal', $params['val'])
                    ->where('jadwal.nip', $nip);
            })
            ->orderByDesc('jadwal.created_at')
            ->first();

        if (!is_null($presensiUser)) {
            $presensiUser->tanggal_cast = Carbon::createFromFormat('Y-m-d', $presensiUser->tanggal)->format('d-m-Y');
            $presensiUser->mulai_absen_cast = Carbon::createFromFormat('H:i', $presensiUser->jam_masuk)->subMinutes($presensiUser->mulai_absen)->format('H:i');
        }

        return $presensiUser;
    }

    public function checkAbsenPulang($user)
    {
        $getPresensi = Jadwal::with([
            'presensi' =>  function ($query) {
                $query->select('id', 'id_jadwal', 'masuk', 'pulang', 'status');
            },
            'presensi.presensiTerlambat' =>  function ($query) {
                $query->select([
                    'id',
                    'id_presensi',
                    'jenis',
                    'ket',
                ]);
            },
        ])
            ->where(['nip' => $user->nip, 'tanggal' => now()->format('Y-m-d')])
            ->where(function ($query) {
                $query->where('status', Jadwal::PROGRESS);
            })
            ->whereHas('presensi', function ($query) {
                $query->whereNotNull('masuk')->whereNotNull('pulang');
            })
            ->first([
                'id',
                'nip',
                'tanggal',
                'kode_shift',
                'jam_masuk',
                'jam_pulang',
                'status',
                'libur',
            ]);

        return $getPresensi;
    }

    public function checkJadwal($user)
    {
        return Jadwal::where([
            'nip' => $user->nip,
            'tanggal' => now()->format('Y-m-d'),
            'libur' => 0
        ])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 2);
            })
            // ->active()
            ->orderBy('jam_masuk')
            ->first();
    }

    public function exportExcel($user)
    {
        return Jadwal::where([
            'nip' => $user->nip,
            'tanggal' => now()->format('y-m-d'),
        ])->first();
    }

    public function getPresensi(array $params)
    {
        return Jadwal::join('presensi', 'presensi.id_jadwal', '=', 'jadwal.id')
            ->join('m_karyawan', 'm_karyawan.nip', '=', 'jadwal.nip')
            ->join('m_unit', 'm_unit.id', '=', 'm_karyawan.id_unit')
            ->selectRaw('jadwal.id, jadwal.tanggal, jadwal.nip, m_karyawan.nama, jadwal.shift, jadwal.jam_masuk, jadwal.jam_pulang, jadwal.status, presensi.masuk, presensi.pulang, presensi.status as presensi_status, m_unit.nama as nama_unit')
            ->whereDate('jadwal.tanggal', $params['tgl']->format('Y-m-d'))
            ->get();
    }

    public function checkRadius(array $params)
    {
        $resp = null;

        $lokasi = MLokasi::where('status', Mlokasi::AKTIF)->get();
        foreach ($lokasi as $lok) {
            $radiusParams = [
                "lokasiLat" => $lok->latitude,
                "lokasiLong" => $lok->longitude,
                "Absenlat" => $params['latitude'],
                "Absenlong" => $params['longitude'],
            ];

            $distanceMeters = $this->getRadius($radiusParams);
            $miles = $lok->radius * 0.000621371192;
            if ($distanceMeters > $miles) {
                continue;
                // throw new Exception('Anda diluar jangkauan');
            }

            $resp = $lok;
            break;
        }

        if (is_null($resp)) {
            throw new Exception('Anda diluar jangkauan');
        }

        return $resp;
    }

    public function getRadius(array $params)
    {
        return $this->getDistanceBetweenPoints(
            $params['lokasiLat'],
            $params['lokasiLong'],
            $params['Absenlat'],
            $params['Absenlong']
        );
    }

    public function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2, $unit = 'm')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        }

        return $miles;
    }

    public function presensiUnit(array $params)
    {
        $karyawanUnit = MKaryawan::select('nip', 'nama')
            ->when(count($params['unit']) > 0 && $params['unit'][0] > 0, function ($query) use ($params) {
                $query->where('id_unit', $params['unit']);
            })
            // ->whereHas('jadwal')
            ->notResign()
            ->get();

        if ($karyawanUnit->isEmpty()) {
            throw new Exception('Unit tidak memiliki Karyawan');
        }

        $jadwals = Jadwal::select('id', 'nip', 'kode_shift', 'shift', 'jam_masuk', 'jam_pulang', 'tanggal', 'libur', 'status')
            ->whereIn('nip', $karyawanUnit->pluck('nip'))
            ->when(!is_null($params['month']) || !is_null($params['year']), function ($query) use ($params) {
                $query->whereMonth('tanggal', $params['month'])
                    ->whereYear('tanggal', $params['year']);
            }, function ($query) {
                /**
                 * DEFAULT IS MONTH OR YEAR NULL OR Empty
                 */
                $now = now();
                $query->whereMonth('tanggal', $now->format('m'))
                    ->whereYear('tanggal', $now->format('Y'));
            })
            ->orderBy('tanggal')
            ->get();

        if ($jadwals->isEmpty()) {
            throw new Exception('Jadwal Kosong');
        }

        $resp = [
            "columns" => [],
            "rows" => [],
            "unit" => optional(
                optional(
                    optional($params['user'])->mKaryawan
                )->mUnit
            )->nama,
        ];

        /**
         * SETUP ROWS
         */
        $jadwalUnit = collect([]);
        foreach ($karyawanUnit as $karyawan) {
            $jadwal = $jadwals->filter(function ($item) use ($karyawan) { // JIKA NIP SAMA
                return $item->nip === $karyawan->nip;
            })
                ->groupBy(function ($item) { // GROUP BY TANGGAL
                    return Carbon::make($item['tanggal'])
                        ->locale('id_ID')
                        ->isoFormat('D dddd');
                })
                ->map(function ($jadwal) { // PER SHIFT
                    return $jadwal->map(function ($item) { // FILTER JADWAL
                        if ($item->libur == 1) {
                            return 'L';
                        }

                        $presensi = $item->kode_shift;

                        switch ($item->status) {
                            case Jadwal::TIDAK_HADIR:
                                return $presensi .= ': ALPA';
                            case Jadwal::IZIN:
                                return $presensi .= ': IZIN';
                            case Jadwal::PROGRESS || Jadwal::SELESAI:
                                if (optional($item->presensi)->status === 'TEPAT') {
                                    return $presensi .= ': TEPAT';
                                } else if (optional($item->presensi)->status === 'TELAT') {
                                    return $presensi .= ': TELAT';
                                }
                            default:
                                return $presensi;
                        }
                    });
                })
                ->all();

            $jadwalUnit->push([
                "{$karyawan->nama}" => $jadwal,
                // "nip" => $karyawan->nip
            ]);
        }

        $columns = collect([]);
        /**
         * Ambil jadwal staf yang pertama
         */
        $columnsData = collect(
            array_values($jadwalUnit->first())[0]
        );
        /**
         * jika kosong, maka ambil yang ke 2
         */
        if ($columnsData->keys()->isEmpty()) {
            $columnsData = collect(
                array_values($jadwalUnit->get(1))[0]
            );
        }
        $columns->push("nama");
        $columns->push($columnsData->keys()->all());
        $resp["columns"] = $columns->flatten()->all();
        $resp["rows"] = $jadwalUnit->map(function ($item) {
            // $newItem = collect(array_values($item)[0])->values();
            $newItem = collect(array_values($item)[0]);
            $newItem->prepend(array_keys($item), "nama");

            return $newItem;
        });

        return $resp;
    }

    /**
     *
     * @param array $params // user, year,month,unit
     * @return array
     */
    public function grafikKehadiran(array $params)
    {
        $karyawans = MKaryawan::selectRaw('nip, nama')
            ->where('id_unit', $params['unit'])
            ->orderBy('nama')
            ->notResign()
            ->get();

        $labels = [];
        $series = [
            "Hadir" => [],
            "Alpa" => [],
            "Izin" => [],
        ];

        foreach ($karyawans as $karyawan) {
            $labels[] = $karyawan->nama;

            $jadwal = (int) Jadwal::selectRaw('COUNT(*) AS ttl')
                ->where(function ($query) {
                    $query->where('libur', 0)
                        ->orWhere(function ($query) { // izin cuti
                            $query->where('libur', 1)
                                ->where('status', Jadwal::IZIN);
                        });
                })
                ->where('nip', $karyawan->nip)
                ->where(function ($query) use ($params) {
                    $query->whereMonth('tanggal', $params['month'])
                        ->whereYear('tanggal', $params['year']);
                })
                ->pluck('ttl')
                ->first();

            $cHadir = (int) Jadwal::selectRaw('COUNT(*) AS ttl')
                ->where(function ($query) use ($params, $karyawan) {
                    $query->where('nip', $karyawan->nip)->whereMonth('tanggal', $params['month'])
                        ->whereYear('tanggal', $params['year']);
                })->where(function ($query) {
                    $query->where('status', Jadwal::SELESAI)
                        ->orWhere('status', Jadwal::PROGRESS);
                })
                ->pluck('ttl')
                ->first();

            $cAlpa = (int) Jadwal::selectRaw('COUNT(*) AS ttl')
                ->where(function ($query) use ($params, $karyawan) {
                    $query->where('nip', $karyawan->nip)->whereMonth('tanggal', $params['month'])
                        ->whereYear('tanggal', $params['year']);
                })->where('status', 3)
                ->pluck('ttl')
                ->first();

            $cIzin =  (int) Jadwal::selectRaw('COUNT(*) AS ttl')
                ->where(function ($query) use ($params, $karyawan) {
                    $query->where('nip', $karyawan->nip)->whereMonth('tanggal', $params['month'])
                        ->whereYear('tanggal', $params['year']);
                })->where(function ($query) {
                    $query->where('status', Jadwal::IZIN)
                        ->where('libur', 1);
                })
                ->pluck('ttl')
                ->first();

            $series["Hadir"][] = round($cHadir > 0 ? $cHadir / $jadwal * 100 : 0);
            $series["Alpa"][] = round($cAlpa > 0 ? $cAlpa / $jadwal * 100 : 0);
            $series["Izin"][] = round($cIzin > 0 ? $cIzin / $jadwal * 100 : 0);
        }

        return compact('labels', 'series');
    }
}

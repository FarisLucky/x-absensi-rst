<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\MLokasi;
use App\Models\Presensi;
use App\Models\PresensiTerlambat;
use App\Models\PresensiUser;
use App\Services\PresensiService;
use App\Services\PresensiUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    use ApiResponse;

    private PresensiService $presensiService;
    private PresensiUserService $presensiUserService;

    public function __construct(PresensiService $presensiService, PresensiUserService $presensiUserService)
    {
        $this->presensiService = $presensiService;
        $this->presensiUserService = $presensiUserService;
    }

    public function index()
    {
        try {

            $params = [
                "user" => auth()->user(),
                "filter" => [
                    "status" => request('filter_status'),
                    "bulan" => request('filter_bulan'),
                    "tahun" => request('filter_tahun'),
                ]
            ];

            $presensies = $this->presensiService->index($params);

            return $this->okApiResponse($presensies, 'Berhasil dimuat');
        } catch (\Throwable $th) {
            return $this->okApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {

        try {
            $presensi = Presensi::with(['mLokasi', 'jadwal', 'presensiTerlambat', 'mKaryawan', 'mKaryawan.mUnit'])
                ->where('id', $id)
                ->first();

            return $this->okApiResponse($presensi, 'Berhasil memuat data');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(Request $request)
    {

        try {

            DB::beginTransaction();

            $jadwal = Jadwal::find($request->id_jadwal, [
                'id',
                'tanggal',
                'kode_shift',
                'shift',
                'jam_masuk',
                'jam_pulang',
                'telat_masuk'
            ]);

            $masuk = Carbon::createFromFormat('Y-m-d H:i', "{$jadwal->tanggal} {$jadwal->jam_masuk}");
            $pulang = Carbon::createFromFormat('Y-m-d H:i', "{$jadwal->tanggal} {$jadwal->jam_pulang}");
            if ($masuk->greaterThan($pulang)) {
                $pulang->addDays();
            }
            $checkIn = Carbon::createFromFormat('Y-m-d H:i', "{$jadwal->tanggal} {$request->presensi['masuk']}");
            $ttlKerja = null;
            $ttlTelat = null;
            $telatCopy = $masuk->copy()->addMinutes($jadwal->telat_masuk);
            $status = 'TEPAT';
            if ($checkIn->greaterThan($telatCopy)) {
                $ttlTelat = $masuk->diffInMinutes($checkIn);
                $status = 'TELAT';
            }
            $ttlKerja = $checkIn->diffInMinutes($pulang);

            $presensi = Presensi::create([
                'id_jadwal' => $jadwal->id,
                'tanggal' => $jadwal->tanggal,
                'kd_shift' => $jadwal->kode_shift,
                'shift' => $jadwal->shift,
                'id_lokasi' => MLokasi::where('status', MLokasi::AKTIF)->pluck('id')[0],
                'nip' => $request->nip,
                'nama' => $request->nama,
                'masuk' => $request->presensi['masuk'],
                'pulang' => $request->presensi['pulang'],
                'tgl_pulang' => Carbon::make($request->presensi['tgl_pulang']),
                'device' => $request->presensi['device'],
                'status' => $request->presensi['status'],
                'ttl_kerja' => $ttlKerja,
                'ttltelat' => $ttlTelat,
            ]);
            if (!is_null($request->presensi['masuk']) && is_null($request->presensi['pulang'])) {
                $presensiUser = PresensiUser::where('id_presensi', $presensi->id)->first();
                if (is_null($presensiUser)) {
                    PresensiUser::create(['id_presensi' => $presensi->id, 'id_jadwal' => $presensi->id_jadwal, 'nip' => $presensi->nip]);
                }
                $jadwal->update([
                    'status' => Jadwal::PROGRESS
                ]);
            }

            $terlambat = PresensiTerlambat::where('id_presensi', $presensi->id)->first();
            if ($request->has('presensi_terlambat.ket')) {
                if (!is_null($terlambat)) {
                    $terlambat->update([
                        'ket' => $request->presensi_terlambat['ket']
                    ]);
                } else {
                    PresensiTerlambat::create([
                        'id_presensi' => $presensi->id,
                        'jenis' => 'MASUK',
                        'ket' => $request->presensi_terlambat['ket'],
                    ]);
                }
            }

            if (!is_null($request->presensi['pulang'])) {
                $jadwal->update([
                    'status' => Jadwal::SELESAI
                ]);
            }

            DB::commit();

            return $this->okApiResponse('OK', 'Anda sudah presensi');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function masuk(Request $request)
    {

        try {
            $params = [
                "dateNow" => now(),
                "request" => [
                    'id_jadwal' => $request->id_jadwal,
                    'lok_masuk' => $request->lok_masuk,
                    'latlng_masuk' => $request->latlng_masuk,
                    'presensi' => $request->presensi,
                ],
                "user" => auth()->user(),
            ];
            $presensiMasuk = $this->presensiService->masuk($params);

            return $this->okApiResponse($presensiMasuk, 'Anda sudah presensi');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function pulang(Request $request)
    {

        try {

            $params = [
                "dateNow" => now(),
                "request" => [
                    'id_jadwal' => $request->id_jadwal,
                    'lok_pulang' => $request->lok_pulang,
                    'latlng_pulang' => $request->latlng_pulang,
                    'presensi' => $request->presensi,
                ],
                "user" => auth()->user(),
            ];
            $pulang = $this->presensiService->pulang($params);

            return $this->okApiResponse($pulang, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $presensi = Presensi::where('id', $id)->first();
            $presensi->update([
                'masuk' => $request->masuk,
                'pulang' => $request->pulang,
                'status' => $request->status,
            ]);
            if (!is_null($request->masuk) && is_null($request->pulang)) {
                $presensiUser = PresensiUser::where('id_presensi', $presensi->id)->first();
                if (is_null($presensiUser)) {
                    PresensiUser::create(['id_presensi' => $presensi->id, 'id_jadwal' => $presensi->id_jadwal, 'nip' => $presensi->nip]);
                }
            } else if (!is_null($request->masuk) && !is_null($request->pulang)) {
                $presensiUser = PresensiUser::where('id_presensi')->first();
                if (!is_null($presensiUser)) {
                    $presensiUser->delete();
                }
            }

            $terlambat = PresensiTerlambat::where('id_presensi', $presensi->id)->first();
            if ($request->has('presensi_terlambat.ket')) {
                if (!is_null($terlambat)) {
                    $terlambat->update([
                        'ket' => $request->presensi_terlambat['ket']
                    ]);
                } else {
                    PresensiTerlambat::create([
                        'id_presensi' => $presensi->id,
                        'jenis' => 'MASUK',
                        'ket' => $request->presensi_terlambat['ket'],
                    ]);
                }
            }

            if ($presensi->status === Presensi::TEPAT) {
                $presensi->ttltelat = null;
                $presensi->save();

                if (!is_null($terlambat)) {
                    $terlambat->delete();
                }
            }
            DB::commit();

            return $this->okApiResponse($presensi, 'Berhasil di perbarui');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {

        try {
            $presensi = Presensi::where('id', $id)->first();
            $presensi->update([
                'status' => $request->status,
                'update_by' => auth()->user()->nip
            ]);

            return $this->okApiResponse($presensi, 'Berhasil di perbarui');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    /**
     * Mobile Api
     */

    public function jadwalByNip()
    {
        try {
            $user = auth()->user();

            $jadwal = $this->presensiService->getJadwal($user->nip);

            return $this->okApiResponse($jadwal);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    /**
     * Mobile Api
     */
    public function checkAbsenMasuk()
    {
        try {

            $user = auth()->user();

            $checkAbsen = PresensiUser::with([
                'presensi' =>  function ($query) {
                    $query->select('id', 'id_jadwal', 'masuk', 'pulang', 'status');
                },
            ])
                ->select(
                    'id',
                    'nip',
                    'id_presensi',
                    'id_jadwal',
                )
                ->where('nip', $user->nip)
                ->latest()
                ->first();

            if (!is_null($checkAbsen)) {
                return $this->okApiResponse($checkAbsen);
            }

            $checkAbsen = $this->presensiService->checkAbsenMasuk($user);

            if (is_null(optional($checkAbsen)->presensi)) {
                throw new Exception('Absen Masuk tidak ada');
            }

            return $this->okApiResponse(optional($checkAbsen)->presensi);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    /**
     * Mobile Api
     */
    public function checkAbsenPulang()
    {
        try {

            $user = auth()->user();

            $checkAbsen = $this->presensiService->checkAbsenPulang($user);

            return $this->okApiResponse(optional($checkAbsen)->presensi);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    /**
     * Mobile Api
     */
    public function checkJadwal()
    {
        try {

            $user = auth()->user();

            $checkAbsen = $this->presensiUserService->get($user->nip);
            if (!is_null($checkAbsen)) {
                return $this->okApiResponse($checkAbsen->jadwal);
            }

            $jadwal = Jadwal::selectIdx()
                ->where([
                    'nip' => $user->nip,
                    'tanggal' => now()->format('Y-m-d'),
                ])
                ->where(function ($query) {
                    $query->whereNull('status')->orWhere('status', Jadwal::PROGRESS);
                })
                ->active()
                // ->orderBy('jam_masuk')
                ->first();

            if (is_null($jadwal)) {
                throw new Exception('Tidak ada jadwal hari ini');
            }

            return $this->okApiResponse($jadwal);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function radiusValidation(Request $request)
    {
        try {

            $params = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                // 'id_lokasi' => optional($request)->id_lokasi
            ];

            $checkPresensi = $this->presensiService->checkRadius($params);

            return $this->okApiResponse($checkPresensi);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function alasanTelat(Request $request, $id)
    {
        try {
            PresensiTerlambat::where('id', $id)
                ->update([
                    "ket" => $request->ket,
                ]);

            return $this->okApiResponse('OK');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

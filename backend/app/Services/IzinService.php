<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\IzinDetail;
use App\Models\IzinKrs;
use App\Models\Jadwal;
use App\Models\MIzin;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IzinService
{
    public function izinCuti($params)
    {
        try {

            DB::beginTransaction();

            $mIzin = MIzin::where('kode', $params['request']['izin'])->first();
            $mulai = Carbon::createFromFormat('Y-m-d', $params['request']['mulai']);
            $akhir = Carbon::createFromFormat('Y-m-d', $params['request']['akhir']);

            $karyawan = $params['user']->mKaryawan;

            if ($karyawan->jml_cuti < 1 && $mIzin->tahunan) {
                throw new Exception('CUTI SUDAH HABIS');
            }

            $periode = $params['request']['periode'];
            $tahunan = $mIzin->tahunan;
            $createdBy = $params['user']->nip;

            $payloadIzin = [
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama,
                'unit' => $karyawan->unit,
                'jabatan' => $karyawan->unit,
                'idm_izin' => $mIzin->id,
                'kode_izin' => $mIzin->kode,
                'izin' => $mIzin->nama,
                'mulai' => $mulai->format('Y-m-d'),
                'akhir' => $akhir->format('Y-m-d'),
                'masuk' => $params['request']['masuk'],
                'periode' => $periode,
                'cuti_diambil' => $tahunan ? $periode : null,
                'sisa' => $tahunan ? $karyawan->jml_cuti - $periode : null,
                'ket' => $params['request']['ket'],
                'created_by' => $createdBy,
                'acc_status' => Izin::PENGAJUAN,
            ];

            $izin = Izin::create($payloadIzin);

            DB::commit();

            return $izin;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function accSubmit(array $params)
    {
        try {

            $request = $params['request'];
            $user = $params['user'];
            $now = now();

            /**
             * GET IZIN
             */

            $izin = Izin::find($request['id_izin']);

            switch ($request['type']) {
                case 'acc_manajemen':

                    /**
                     * UPDATE CUTI SDM APPROVED
                     */
                    $this->updateCuti($izin, $params);

                    $karyawan = $user->mKaryawan;
                    $izin->update([
                        'acc_nip' => $karyawan->nip,
                        'acc_nama' => $karyawan->nama,
                        'acc_at' => $now,
                        'acc_status' => Izin::SELESAI
                    ]);

                    break;
            }

            return $izin;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateCuti(Izin $izin, array $params)
    {
        try {
            DB::beginTransaction();

            $izinDetails = [];
            $now = now();
            $mIzin = MIzin::where('kode', $izin->kode_izin)->first();
            $karyawan = MKaryawan::where('nip', $izin->nip)->first();

            if ($mIzin->tahunan) {
                $karyawan->update([
                    'jml_cuti' => $izin->sisa
                ]);
            }

            $periodeCuti = CarbonPeriod::create($izin->mulai, $izin->akhir);
            foreach ($periodeCuti as $periode) {
                /**
                 * Cari Jadwal
                 */
                $checkJadwal = Jadwal::where([
                    'tanggal' => $periode->format('Y-m-d'),
                    'libur' => 0,
                    'nip' => $izin->nip,
                ])
                    ->first();
                /**
                 * CHECK JADWAL ADA ATAU TIDAK
                 */
                /**
                 * DETAIL IZIN
                 */
                $izinDetails[] = [
                    'id_izin' => $izin->id,
                    'tanggal' => $periode->format('Y-m-d'),
                    'nip' => $izin->nip,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (!is_null($checkJadwal)) {
                    /** UPDATE JADWAL KARYAWAN YANG MENGAJUKAN IZIN */
                    $checkJadwal->update([
                        'libur' => 1,
                        'status' => Jadwal::IZIN
                    ]);
                } else if (is_null($checkJadwal)) {
                    $payload = [
                        'nip' => $izin->nip,
                        'nama' => $izin->nama,
                        'id_unit' => $karyawan->id_unit,
                        'unit' => $karyawan->unit,
                        'jabatan' => $karyawan->jabatan,
                        'tanggal' => $periode->format('Y-m-d'),
                        'kode_shift' => null,
                        'shift' => null,
                        'mulai_absen' => null,
                        'jam_masuk' => null,
                        'telat_masuk' => null,
                        'jam_pulang' => null,
                        'telat_pulang' => null,
                        'created_by' => $izin->acc_sdm,
                        'libur' => 1,
                        'status' => Jadwal::IZIN
                    ];

                    Jadwal::create($payload);
                }
            }

            IzinDetail::insert($izinDetails);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function checkPulang(Jadwal $jadwal)
    {
        $izin = Izin::with('izinKrs')->where('tanggal', $jadwal->tanggal)
            ->where('jenis_table', Izin::KRS)
            ->where('kode_izin', MIzin::PULANG_CEPAT)
            ->where('nip', $jadwal->nip)
            ->first();

        $result = false;

        if (!is_null($izin)) {
            $now = now();
            $mulai = Carbon::createFromFormat('H:i', $izin->izinKrs->mulai);
            $result = $now->greaterThanOrEqualTo($mulai);
        }

        return $result;
    }

    public function rekapFindByDateRangeAndUnit(array $params)
    {
        $izins = Izin::with([
            'izinKrs' => function ($query) {
                $query->select('id_izin', 'mulai', 'akhir', 'ket');
            },
            'izinCuti' => function ($query) {
                $query->select('id_izin', 'mulai', 'akhir', 'pengganti', 'periode', 'sisa');
            },
            'izinCuti.penggantiBy' => function ($query) {
                $query->select('nip', 'nama');
            },
            'pemohon' => function ($query) {
                $query->select('id', 'nip', 'id_unit');
            },
            'pemohon.mUnit' => function ($query) {
                $query->select('id', 'nama');
            },
        ])
            ->select([
                'id',
                'tanggal',
                'nip',
                'nama',
                'izin',
                'jenis_table',
                'ket',
            ])
            ->whereBetween('tanggal', [$params['start'], $params['end']])
            ->when(count($params['id_unit']) > 0, function ($query) use ($params) {
                $units = [];
                foreach ($params['id_unit'] as $idUnit) {
                    if ((int) $idUnit > 0) {
                        $units[] = $idUnit;
                    }
                }
                if (count($units) > 0) {
                    $query->whereHas('pemohon', function ($query) use ($units) {
                        return $query->whereIn('id_unit', $units);
                    });
                }
            })
            ->where('acc_status', Izin::SELESAI)
            ->orderBy('tanggal')
            ->orderBy('nama')
            ->get();
        // dd($izins);

        return $izins;
    }
}

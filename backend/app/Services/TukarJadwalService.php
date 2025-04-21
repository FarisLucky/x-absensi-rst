<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\JadwalBlue;
use App\Models\JadwalRed;
use App\Models\MKaryawan;
use App\Models\Tolak;
use App\Models\TukarJadwal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TukarJadwalService
{

    private TukarJadwal $tukarJadwal;

    private User $user;

    public function __construct(TukarJadwal $tukarJadwal, User $user)
    {
        $this->tukarJadwal = $tukarJadwal;
        $this->user = $user;
    }

    public function run()
    {
        if ($this->tukarJadwal->jenis === TukarJadwal::TUKAR_SHIFT) {
            $this->tukarShift();
        } elseif ($this->tukarJadwal->jenis === TukarJadwal::TUKAR_OFF_1) {
            $this->tukarOff1();
        }
    }

    public function tukarShift()
    {
        try {

            // DB::beginTransaction();

            $jadwalPihak1 = Jadwal::with('presensi')->where(
                [
                    'tanggal' => $this->tukarJadwal->tgl_pihak1,
                    'nip' => $this->tukarJadwal->nip_pihak1,
                    'kode_shift' => $this->tukarJadwal->kd_shift_pihak1,
                ]
            )->first();


            $jadwalPihak2 = Jadwal::with('presensi')->where(
                [
                    'tanggal' => $this->tukarJadwal->tgl_pihak2,
                    'nip' => $this->tukarJadwal->nip_pihak2,
                    'kode_shift' => $this->tukarJadwal->kd_shift_pihak2,
                ]
            )->first();

            if (is_null($jadwalPihak1)) {
                $payload = [
                    'id_relation' => $this->tukarJadwal->id,
                    'ket' => 'Jadwal Pihak 1 tidak ditemukan',
                    'jenis' => Tolak::TUKARJADWAL,
                    'created_by' => auth()->user()->nip,
                ];

                Tolak::create($payload);
                return;
            } else if (is_null($jadwalPihak2)) {

                $payload = [
                    'id_relation' => $this->tukarJadwal->id,
                    'ket' => 'Jadwal Pihak 2 tidak ditemukan',
                    'jenis' => Tolak::TUKARJADWAL,
                    'created_by' => auth()->user()->nip,
                ];

                Tolak::create($payload);
                return;
            }

            /**
             * ACTIVE VALIDATE
             */
            if (\App\Utils\JadwalValidation::notActive($jadwalPihak1)) {
                throw new \Exception('Jadwal Phk 1 belum divalidasi Kabag/Kabid/Direktur');
            } else if (\App\Utils\JadwalValidation::notActive($jadwalPihak2)) {
                throw new \Exception('Jadwal Phk 2 belum divalidasi Kabag/Kabid/Direktur');
            }

            if (!is_null($jadwalPihak1->presensi) || !is_null($jadwalPihak2->presensi)) {
                $ket = '';
                if (!is_null($jadwalPihak1->presensi)) {
                    $ket = 'Phk 1 sudah presensi';
                } else if (!is_null($jadwalPihak2->presensi)) {
                    $ket = 'Phk 2 sudah presensi';
                }

                $payload = [
                    'id_relation' => $this->tukarJadwal->id,
                    'ket' =>  $ket,
                    'jenis' => Tolak::TUKARJADWAL,
                    'created_by' => auth()->user()->nip,
                ];

                Tolak::create($payload);
                return;
            }

            $jadwalPihak1Clone = Jadwal::where(
                [
                    'tanggal' => $this->tukarJadwal->tgl_pihak1,
                    'nip' => $this->tukarJadwal->nip_pihak1,
                    'kode_shift' => $this->tukarJadwal->kd_shift_pihak1,
                ]
            )->first();

            if (is_null($jadwalPihak1) || is_null($jadwalPihak2)) {
                throw new \Exception('Salah satu jadwal tidak ditemukan');
            }

            $jadwalPihak1->update([
                'kode_shift' => $jadwalPihak2->kode_shift,
                'shift' => $jadwalPihak2->shift,
                'mulai_absen' => $jadwalPihak2->mulai_absen,
                'jam_masuk' => $jadwalPihak2->jam_masuk,
                'telat_masuk' => $jadwalPihak2->telat_masuk,
                'jam_pulang' => $jadwalPihak2->jam_pulang,
                'telat_pulang' => $jadwalPihak2->telat_pulang,
                'update_by' => $this->user->nip,
                'libur' => $jadwalPihak2->libur,
            ]);

            $jadwalPihak2->update([
                'kode_shift' => $jadwalPihak1Clone->kode_shift,
                'shift' => $jadwalPihak1Clone->shift,
                'mulai_absen' => $jadwalPihak1Clone->mulai_absen,
                'jam_masuk' => $jadwalPihak1Clone->jam_masuk,
                'telat_masuk' => $jadwalPihak1Clone->telat_masuk,
                'jam_pulang' => $jadwalPihak1Clone->jam_pulang,
                'telat_pulang' => $jadwalPihak1Clone->telat_pulang,
                'update_by' => $this->user->nip,
                'libur' => $jadwalPihak1Clone->libur,
            ]);

            $this->changeJadwalCodeBlue($this->tukarJadwal);
            $this->changeJadwalCodeRed($this->tukarJadwal);

            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();

            throw $th;
        }
    }

    public function tukarOff1()
    {
        try {

            $jadwalService = new JadwalService();

            $jadwalPihak1 = Jadwal::with('presensi')->where(
                [
                    'tanggal' => $this->tukarJadwal->tgl_pihak1,
                    'nip' => $this->tukarJadwal->nip_pihak1,
                    'kode_shift' => $this->tukarJadwal->kd_shift_pihak1,
                ]
            )->first();

            $jadwalPihak2 = Jadwal::with('presensi')->where(
                [
                    'tanggal' => $this->tukarJadwal->tgl_pihak1,
                    'nip' => $this->tukarJadwal->nip_pihak2,
                    'kode_shift' => $this->tukarJadwal->kd_shift_pihak1,
                ]
            )->first();

            if (\App\Utils\JadwalValidation::notActive($jadwalPihak1)) {
                throw new \Exception('Jadwal Phk 1/2 belum divalidasi Kabag/Kabid/Direktur');
            }

            if (!is_null(optional($jadwalPihak1)->presensi) || !is_null(optional($jadwalPihak2)->presensi)) {
                throw new \Exception('Gagal! Jadwal Phk 1/2 sudah presensi');
            }

            if (!is_null($jadwalPihak2)) {
                $jadwalPihak2->update([
                    'kode_shift' => $jadwalPihak1->kode_shift,
                    'shift' => $jadwalPihak1->shift,
                    'mulai_absen' => $jadwalPihak1->mulai_absen,
                    'jam_masuk' => $jadwalPihak1->jam_masuk,
                    'telat_masuk' => $jadwalPihak1->telat_masuk,
                    'jam_pulang' => $jadwalPihak1->jam_pulang,
                    'telat_pulang' => $jadwalPihak1->telat_pulang,
                    'update_by' => $this->user->nip,
                    'libur' => $jadwalPihak1->libur,
                ]);
            } else {
                $karyawan = MKaryawan::where('nip', $this->tukarJadwal->nip_pihak2)->first();
                Jadwal::create([
                    'nip' => $this->tukarJadwal->nip_pihak2,
                    'tanggal' => $jadwalPihak1->tanggal,
                    'nama' => $this->tukarJadwal->nama_pihak2,
                    'kode_shift' => $jadwalPihak1->kode_shift,
                    'shift' => $jadwalPihak1->shift,
                    'mulai_absen' => $jadwalPihak1->mulai_absen,
                    'jam_masuk' => $jadwalPihak1->jam_masuk,
                    'telat_masuk' => $jadwalPihak1->telat_masuk,
                    'jam_pulang' => $jadwalPihak1->jam_pulang,
                    'telat_pulang' => $jadwalPihak1->telat_pulang,
                    'update_by' => $this->user->nip,
                    'created_by' => $this->user->nip,
                    'libur' => $jadwalPihak1->libur,
                    'status' => null,
                    'validate_by' => $jadwalService->getValidateJadwal($karyawan),
                    'validate_at' => $jadwalService->setBySdm($this->user),
                ]);
            }

            /**
             * JADWAL PIHAK 1 LIBUR
             */
            if (!is_null($jadwalPihak1)) {
                $jadwalPihak1->update([
                    'update_by' => $this->user->nip,
                    'libur' => 1,
                    'status' => Jadwal::TUKAR_OFF
                ]);
            }

            $this->changeJadwalCodeBlue($this->tukarJadwal);
            $this->changeJadwalCodeRed($this->tukarJadwal);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function changeJadwalCodeBlue(TukarJadwal $tukarJadwal)
    {
        try {

            $pihak1 = $tukarJadwal->nip_pihak1;
            $pihak2 = $tukarJadwal->nip_pihak2;

            $codeBlue = JadwalBlue::where('tanggal', $tukarJadwal->tgl_pihak1)
                ->where(function ($query) use ($pihak1) {
                    $query->where('lead', $pihak1)
                        ->orWhere('vent', $pihak1)
                        ->orWhere('komp', $pihak1)
                        ->orWhere('sirk', $pihak1)
                        ->orWhere('sec', $pihak1);
                })
                ->first();
            if (!is_null($codeBlue)) {
                $column = '';
                foreach (json_decode($codeBlue, true) as $key => $value) {
                    if ($value === strval($pihak1)) {
                        $column = $key;
                        break;
                    }
                }
                $codeBlue->update([
                    $column => $pihak2
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function changeJadwalCodeRed(TukarJadwal $tukarJadwal)
    {
        try {

            $pihak1 = $tukarJadwal->nip_pihak1;
            $pihak2 = $tukarJadwal->nip_pihak2;

            $codeBlue = JadwalRed::where('tanggal', $tukarJadwal->tgl_pihak1)
                ->where(function ($query) use ($pihak1) {
                    $query->where('api', $pihak1)
                        ->orWhere('dok', $pihak1)
                        ->orWhere('pasien', $pihak1)
                        ->orWhere('aset', $pihak1);
                })
                ->first();
            if (!is_null($codeBlue)) {
                $column = '';
                foreach (json_decode($codeBlue, true) as $key => $value) {
                    if ($value === strval($pihak1)) {
                        $column = $key;
                        break;
                    }
                }
                $codeBlue->update([
                    $column => $pihak2
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    // public function tukarOff2()
    // {
    //     try {

    //         DB::beginTransaction();

    //         $jadwalPihak1 = Jadwal::where(
    //             [
    //                 'tanggal' => $this->tukarJadwal->tgl_pihak1,
    //                 'nip' => $this->tukarJadwal->nip_pihak1,
    //                 'kode_shift' => $this->tukarJadwal->kd_shift_pihak1,
    //             ]
    //         )->first();

    //         $jadwalPihak2 = Jadwal::where(
    //             [
    //                 'tanggal' => $this->tukarJadwal->tgl_pihak2,
    //                 'nip' => $this->tukarJadwal->nip_pihak2,
    //             ]
    //         )->get();

    //         if (!is_null($jadwalPihak2)) {
    //             foreach ($jadwalPihak2 as $pihak2) {
    //                 $pihak2->delete();
    //             }
    //         }
    //         Jadwal::create([
    //             'nip' => $this->tukarJadwal->nip_pihak2,
    //             'tanggal' => $jadwalPihak1->tanggal,
    //             'nama' => $this->tukarJadwal->nama_pihak2,
    //             'kode_shift' => $jadwalPihak1->kode_shift,
    //             'shift' => $jadwalPihak1->shift,
    //             'mulai_absen' => $jadwalPihak1->mulai_absen,
    //             'jam_masuk' => $jadwalPihak1->jam_masuk,
    //             'telat_masuk' => $jadwalPihak1->telat_masuk,
    //             'jam_pulang' => $jadwalPihak1->jam_pulang,
    //             'telat_pulang' => $jadwalPihak1->telat_pulang,
    //             'update_by' => $this->user->nip,
    //             'created_by' => $this->user->nip,
    //             'libur' => 0,
    //             'status' => null
    //         ]);

    //         /**
    //          * JADWAL PIHAK 1 LIBUR
    //          */
    //         if (!is_null($jadwalPihak1)) {
    //             $jadwalPihak1->update([
    //                 'kode_shift' => null,
    //                 'shift' => null,
    //                 'mulai_absen' => null,
    //                 'jam_masuk' => null,
    //                 'telat_masuk' => null,
    //                 'jam_pulang' => null,
    //                 'telat_pulang' => null,
    //                 'update_by' => $this->user->nip,
    //                 'libur' => 1
    //             ]);
    //         }

    //         DB::commit();
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //     }
    // }
}

<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\MKaryawan;
use App\Models\MShift;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class JadwalImport implements ToCollection, WithStartRow, WithHeadingRow
{

    private string $year;

    private string $month;

    public function __construct(array $params)
    {
        $this->year = $params['year'];
        $this->month = $params['month'];
    }

    public function collection(Collection $rows)
    {
        $payload = [];
        $user = auth()->user();

        /**
         * INSERT JADWAL
         */
        try {
            DB::beginTransaction();
            foreach ($rows as $row) {

                $nip = $row['nip'];
                $karyawan = MKaryawan::where('nip', $nip)->select('nip', 'nama', 'id_unit', 'unit', 'jabatan')->first();
                if ((is_null($row['nip']) || $row['nip'] === '') && is_null($karyawan)) {
                    continue;
                }
                foreach ($row->toArray() as $key => $value) {
                    if ((int) $key === 0) {
                        continue;
                    }

                    if ((int) $key > 31) {
                        break;
                    }

                    /**
                     * CEK JUDUL DIUBAH ATAU TIDAK, DAN AMBIL TANGGAL
                     */
                    $getTgl = explode('_', $key);
                    if (count($getTgl) < 1) {
                        throw new Exception('Jangan Merubah Judul Tanggal');
                    }

                    $tgl = Carbon::create($this->year, $this->month, $getTgl[0]);
                    $value = strtoupper($value);

                    /**
                     * HAPUS JADWAL DULU
                     */
                    $jadwalByNip = Jadwal::where('nip', $row['nip'])
                        ->whereDate('tanggal', $tgl->format('Y-m-d'))
                        ->get();

                    if (count($jadwalByNip) > 0) {
                        /**
                         * HAPUS SEMUA JADWAL YANG STATUS NULL
                         */
                        foreach ($jadwalByNip as $jadwalKaryawan) {
                            if (is_null($jadwalKaryawan->status)) {
                                $jadwalKaryawan->delete();
                            }
                        }
                    }


                    if ($value == 'L') {
                        $payload[] = [
                            'tanggal' => $tgl->format('Y-m-d'),
                            'nip' => $row['nip'],
                            'nama' => $row['nama'],
                            'id_unit' => $karyawan->id_unit,
                            'unit' => $karyawan->unit,
                            'jabatan' => $karyawan->jabatan,
                            'kode_shift' => null,
                            'shift' => null,
                            'mulai_absen' => null,
                            'jam_masuk' => null,
                            'telat_masuk' => null,
                            'jam_pulang' => null,
                            'telat_pulang' => null,
                            'created_by' => $user->nip,
                            'libur' => 1,
                            'status' => null,
                            'created_at' => now(),
                        ];
                        // }
                    } else {
                        /**
                         * CHECK JADWAL DOUBLE SHIFT
                         */
                        $doubleShift = explode(',', $value);
                        if (count($doubleShift) > 1) {
                            foreach ($doubleShift as $kode) {
                                /**
                                 * JIKA SHIFT KOSONG, LEWATI
                                 */
                                $shift = MShift::where('kode', $kode)->first();
                                if (is_null($shift)) {
                                    continue;
                                }
                                $payload[] = [
                                    'tanggal' => $tgl->format('Y-m-d'),
                                    'nip' => $row['nip'],
                                    'nama' => $row['nama'],
                                    'id_unit' => $karyawan->id_unit,
                                    'unit' => $karyawan->unit,
                                    'jabatan' => $karyawan->jabatan,
                                    'kode_shift' => $shift->kode,
                                    'shift' => $shift->nama,
                                    'mulai_absen' => $shift->mulai_absen,
                                    'jam_masuk' => $shift->jam_masuk,
                                    'telat_masuk' => $shift->telat_masuk,
                                    'jam_pulang' => $shift->jam_pulang,
                                    'telat_pulang' => $shift->telat_pulang,
                                    'created_by' => $user->nip,
                                    'libur' => 0,
                                    'status' => null,
                                    'created_at' => now(),
                                ];
                            }
                        } else {

                            $shift = MShift::where('kode', $value)->first();
                            if (is_null($shift)) {
                                continue;
                            }
                            $payload[] = [
                                'tanggal' => $tgl->format('Y-m-d'),
                                'nip' => $row['nip'],
                                'nama' => $row['nama'],
                                'id_unit' => $karyawan->id_unit,
                                'unit' => $karyawan->unit,
                                'jabatan' => $karyawan->jabatan,
                                'kode_shift' => $shift->kode,
                                'shift' => $shift->nama,
                                'mulai_absen' => $shift->mulai_absen,
                                'jam_masuk' => $shift->jam_masuk,
                                'telat_masuk' => $shift->telat_masuk,
                                'jam_pulang' => $shift->jam_pulang,
                                'telat_pulang' => $shift->telat_pulang,
                                'libur' => 0,
                                'created_by' => $user->nip,
                                'status' => null,
                                'created_at' => now(),
                            ];
                        }
                    }
                }
            }

            Jadwal::insert($payload);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function startRow(): int
    {
        return 9;
    }

    public function headingRow(): int
    {
        return 8;
    }
}

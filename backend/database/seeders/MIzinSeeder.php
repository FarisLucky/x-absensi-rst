<?php

namespace Database\Seeders;

use App\Models\MIzin;
use Illuminate\Database\Seeder;

class MIzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        $data = [
            [
                'kode' => 'ICT',
                'nama' => 'IZIN CUTI TAHUNAN',
                'acc1' => 1,
                'acc2' => 1,
                'acc3' => 1,
                'acc_sdm' => 1,
                'tahunan' => 1,
                'inputan' => 'hari',
                'bukti' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'IK',
                'nama' => 'IZIN KELUAR',
                'acc1' => 1,
                'acc2' => 0,
                'acc3' => 0,
                'acc_sdm' => 1,
                'tahunan' => 0,
                'inputan' => 'jam',
                'bukti' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'IPC',
                'nama' => 'IZIN PULANG CEPAT',
                'acc1' => 1,
                'acc2' => 0,
                'acc3' => 0,
                'acc_sdm' => 1,
                'tahunan' => 0,
                'inputan' => 'jam',
                'bukti' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'IS',
                'nama' => 'IZIN SAKIT',
                'acc1' => 1,
                'acc2' => 0,
                'acc3' => 0,
                'acc_sdm' => 1,
                'tahunan' => 0,
                'inputan' => 'hari',
                'bukti' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ILL',
                'nama' => 'IZIN LAIN LAIN',
                'acc1' => 1,
                'acc2' => 0,
                'acc3' => 0,
                'acc_sdm' => 1,
                'tahunan' => 0,
                'inputan' => 'hari',
                'bukti' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ICM',
                'nama' => 'IZIN CUTI MELAHIRKAN',
                'acc1' => 1,
                'acc2' => 1,
                'acc3' => 1,
                'acc_sdm' => 1,
                'tahunan' => 0,
                'inputan' => 'hari',
                'bukti' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        MIzin::insert($data);
    }
}

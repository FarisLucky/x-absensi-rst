<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = now();
        $data = [
            [
                'nama' => 'IZIN SAKIT',
                'jml_acc' => 1,
                'tahunan' => 0,
                'acc_by' => 160,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'nama' => 'IZIN KELUAR',
                'jml_acc' => 1,
                'tahunan' => 0,
                'acc_by' => 160,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'nama' => 'IZIN CUTI',
                'jml_acc' => 3,
                'tahunan' => 0,
                'acc_by' => 160,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ];


    }
}

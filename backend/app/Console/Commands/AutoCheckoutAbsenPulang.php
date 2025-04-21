<?php

namespace App\Console\Commands;

use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\PresensiUser;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AutoCheckoutAbsenPulang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkout:absen-pulang {month?} {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Checkout Absen pulang apabila karyawan lupa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $month = $this->argument('month');
            $year = $this->argument('year');
            $jadwals = Jadwal::where('status', Jadwal::PROGRESS)
                ->when(
                    $month && $year,
                    function ($query) use ($month, $year) {
                        $query->where(function ($query)  use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                    },
                    function ($query) {
                        $query->where(function ($query) {
                            $query->whereMonth('tanggal', now()->format('m'))
                                ->whereYear('tanggal', now()->format('Y'));
                        });
                    }
                )
                ->get();

            if (count($jadwals) > 0) {
                foreach ($jadwals as $jadwal) {

                    try {
                        DB::beginTransaction();
                        $pulang = Carbon::createFromFormat('Y-m-d H:i', $jadwal->tanggal . " {$jadwal->jam_pulang}");
                        $pulangCopy = Carbon::createFromFormat('Y-m-d H:i', $jadwal->tanggal . " {$jadwal->jam_pulang}");
                        $masuk = Carbon::createFromFormat('Y-m-d H:i', $jadwal->tanggal . " {$jadwal->jam_masuk}");
                        if ($masuk->greaterThan($pulang)) {
                            $pulang->addDay();
                            $pulangCopy->addDay();
                        }
                        $pulang->addMinutes($jadwal->telat_pulang);

                        if (now()->greaterThan($pulang)) {
                            $checkIn = Carbon::createFromFormat('Y-m-d H:i', $jadwal->tanggal . " {$jadwal->masuk}");

                            $jadwal->tgl_pulang = $pulang->format('Y-m-d');
                            $jadwal->pulang = $jadwal->jam_pulang;
                            $jadwal->auto = Jadwal::AUTOPLG;
                            $jadwal->ttl_kerja = $checkIn->diffInMinutes($pulangCopy);
                            $jadwal->save();

                            $sessionPresensi = PresensiUser::where('id_jadwal', $jadwal->id)->first();
                            if (!is_null($sessionPresensi)) {
                                $sessionPresensi->delete();
                            }

                            $jadwal->status = Jadwal::SELESAI;
                            $jadwal->save();
                        }
                        DB::commit();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                    }
                }
            }

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

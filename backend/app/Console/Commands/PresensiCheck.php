<?php

namespace App\Console\Commands;

use App\Models\Jadwal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PresensiCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan checking jadwal yang sudah lewat atau tidak';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $yestrday = now()->subDay();
        $jadwals = Jadwal::whereDate('tanggal', $yestrday)->where(function ($query) {
            $query->whereNull('status')->where('libur', 0);
        })
            // ->active()
            ->get();

        if (!is_null($jadwals)) {
            foreach ($jadwals as $jadwal) {
                $jadwal->update([
                    'status' => Jadwal::TIDAK_HADIR
                ]);
            }
        }
        
        Log::info('RUN SCHEDULING TELAT');

        return 'BERHASIL MENJADIKAN TELAT';
    }
}

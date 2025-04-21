<?php

use App\Http\Controllers\Api\HistoryKaryawanController;
use App\Http\Controllers\Api\HistoryPresensiController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\MKaryawanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RekapIzinController;
use App\Http\Controllers\Api\RekapJadwalController;
use App\Http\Controllers\Api\RekapLemburController;
use App\Http\Controllers\Api\RekapPresensiController;
use App\Http\Controllers\Api\RekapTerlambatController;
use App\Http\Controllers\Api\TukarJadwalController;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware groudetail_pembelian. Now create something great!
|
*/

Route::get('/', function () {
    return 'tesy';
    abort(404);
});


/** jadwal/template?jenis=excel */
Route::get('clear', function () {
    Artisan::call('optimize:clear');

    return 'OK';
});
// Route::get('jadwal/template', [JadwalController::class, 'downloadTemplate'])->name('jadwal.download');
// Route::get('jadwal/update', [TukarJadwalController::class, 'tukarShift'])->name('jadwal.tukarShift');
// Route::get('izin/bukti/{id}', [IzinController::class, 'bukti'])->name('izin.bukti');
Route::get('profil/{nip}', [ProfileController::class, 'getProfil'])->name('profil.get_profil');
// /**
//  * KARYAWAN EXPORT
//  */
// Route::get('m-karyawan/export', [MKaryawanController::class, 'exportExcel'])->name('m-karyawan.export');
// Route::get('test', function () {
//     dd(Storage::disk('nasgs')->allDirectories());
// });
// /**
//  * REKAP EXPORT
//  */
// Route::get('rekap/karyawan/export', [HistoryKaryawanController::class, 'exportExcel'])->name('rekap.karyawan');
// Route::get('rekap/presensi-bulanan/export', [RekapPresensiController::class, 'exportPresensiBulanan'])->name('rekap.export_presensi_bulanan');
// Route::get('rekap-bulanan/export', [RekapPresensiController::class, 'rekapPresensiBulanan'])->name('rekap.rekap_bulanan');
// Route::get('rekap/izin/export', [RekapPresensiController::class, 'exportIzinExcel'])->name('rekap.export_izin_excel');
// Route::get('rekap/lembur/excel', [RekapLemburController::class, 'bulananExcel'])->name('rekap.export_lembur_excel');
// Route::get('rekap/terlambat/excel', [RekapTerlambatController::class, 'bulananExcel'])->name('rekap.export_telat_excel');
// Route::get('rekap/izin/excel', [RekapIzinController::class, 'bulananExcel'])->name('rekap.export_izin_excel');
// Route::get('rekap/jadwal/export', [RekapJadwalController::class, 'bulananExcel'])->name('rekap.export_jadwal_excel');
// /**
//  * history
//  */
// Route::get('history/presensi-harian/export', [HistoryPresensiController::class, 'exportHarianExcel'])->name('history.export_presensi_harian');

Route::get('update-user', function () {
    $users = DB::table('users_bak')->get();
    foreach ($users as $user) {
        $u = App\Models\User::where('nip', $user->nip)->first();
        if (!is_null($u)) {
            // $user->deviceweb_id = $u->deviceweb_id;
            DB::table('users_bak')->where('nip', $user->nip)
                ->update([
                    'deviceweb_id' => $u->deviceweb_id
                ]);
        }
    }
});

// Route::get('update-validate', function () {
//     $jadwal = Jadwal::where(function ($query) {
//         $query->whereMonth('tanggal', '08')
//             ->whereYear('tanggal', ' 2024');
//     })
//         ->where('validate_by', 181012013)
//         ->get();

//     foreach ($jadwal as $j) {
//         $j->update([
//             'validate_by' => 41112010
//         ]);
//     }

//     return 'OK';
// });

// Route::get('update-validate-by', function () {
//     $jadwalService = new JadwalService();
//     DB::beginTransaction();
//     $jadwals = Jadwal::where(function ($query) {
//         $query->whereMonth('tanggal', 8)->whereYear('tanggal', 2024);
//     })
//         ->whereNull('validate_by')
//         ->chunk(100, function ($jadwals) use ($jadwalService) {

//             foreach ($jadwals as $jadwal) {
//                 $karyawan = \App\Models\MKaryawan::where('nip', $jadwal->nip)->first();
//                 $jadwal->update([
//                     'validate_by' =>  $jadwalService->getValidateJadwal($karyawan)
//                 ]);
//             }
//         });
//     // dd($jadwals->get(1));
//     DB::commit();

//     dd('OK GAS');
// });

// Route::get('update-izin-detail', function () {
//     $izins = Izin::with('izinCuti')
//         ->where('acc_status', Izin::SELESAI)
//         ->where('jenis_table', Izin::CUTI)
//         ->get();
//     // dd($izins);

//     foreach ($izins as $izin) {
//         $periods = CarbonPeriod::create($izin->izinCuti->mulai, $izin->izinCuti->akhir);
//         foreach ($periods as $period) {
//             // $payload = [
//             //     'id_izin' => $izin->id,
//             //     'tanggal' => $period->format('Y-m-d'),
//             //     'nip' => $izin->nip,
//             //     'pengganti' => $izin->izinCuti->pengganti,
//             // ];
//             // IzinDetail::create($payload);
//         }
//         dump($izin->izinCuti->mulai . " " . $izin->izinCuti->akhir . " = " . $izin->nip);
//     }
// });

// Route::get('storage-link', function () {
//     Artisan::call('storage:link');
// });

// Route::get('update-melahirkan', function () {
//     try {
//         DB::beginTransaction();

//         $izin = Izin::with('izinCuti')
//             ->where('id', 144)
//             ->first();
//         $izinDetails = [];
//         $now = now();
//         $periodeCuti = CarbonPeriod::create($izin->izinCuti->mulai, $izin->izinCuti->akhir);
//         foreach ($periodeCuti as $periode) {
//             /**
//              * UPDATE JADWAL KARYAWAN YANG MENGAJUKAN IZIN
//              */
//             $checkJadwal = Jadwal::where([
//                 'tanggal' => $periode->format('Y-m-d'),
//                 'nip' => $izin->nip,
//                 'libur' => 0
//             ])->first();

//             /**
//              * CHECK JADWAL ADA ATAU TIDAK
//              */
//             if (!is_null($checkJadwal)) {
//                 $checkJadwal->update([
//                     'update_by' => '222082017',
//                     'libur' => 1,
//                     'status' => Jadwal::IZIN
//                 ]);
//                 /**
//                  * DETAIL IZIN
//                  */
//                 $izinDetails += [
//                     'id_izin' => $izin->id,
//                     'tanggal' => $periode->format('Y-m-d'),
//                     'nip' => $izin->nip,
//                     'pengganti' => $izin->izinCuti->pengganti,
//                     'created_at' => $now,
//                     'updated_at' => $now,
//                 ];
//             } else if (is_null($checkJadwal) && $izin->kode_izin === Izin::LAHIRAN) {

//                 $payload = [
//                     'nip' => $izin->nip,
//                     'tanggal' => $periode->format('Y-m-d'),
//                     'nama' => $izin->nama,
//                     'kode_shift' => null,
//                     'shift' => null,
//                     'mulai_absen' => null,
//                     'jam_masuk' => null,
//                     'telat_masuk' => null,
//                     'jam_pulang' => null,
//                     'telat_pulang' => null,
//                     'validate_by' => $izin->acc_sdm,
//                     'validate_at' => now(),
//                     'update_by' => $izin->acc_sdm,
//                     'created_by' => $izin->acc_sdm,
//                     'libur' => 1,
//                     'status' => Jadwal::IZIN
//                 ];
//                 Jadwal::create($payload);
//                 $izinDetails += [
//                     'id_izin' => $izin->id,
//                     'tanggal' => $periode->format('Y-m-d'),
//                     'nip' => $izin->nip,
//                     'pengganti' => $izin->izinCuti->pengganti,
//                     'created_at' => $now,
//                     'updated_at' => $now,
//                 ];
//             }
//         }
//         IzinDetail::insert($izinDetails);
//         DB::commit();
//     } catch (\Throwable $th) {
//         DB::rollback();
//         dd($th->getMessage());
//         // dd($th->getTraceAsString());
//         //throw $th;
//     }
// });

Route::get('remove-duplicate-jadwal', function () {
    $idsToKeep = DB::table('jadwal')
        ->select(DB::raw('MIN(id) as id'))
        ->groupBy('tanggal', 'nip', 'kode_shift') // Add other columns if needed
        ->where(function ($query) {
            $query->whereMonth('tanggal', '12')
                ->whereYear('tanggal', '2024');
        })
        ->where(function ($query) {
            $query->where('status', 1)->orWhere('status', 2)->orWhere('status', 4)->orWhere('status', '=', null);
        })
        ->where('id_unit', 24)
        ->pluck('id');

    // Delete duplicates while keeping the first occurrence
    DB::table('jadwal')
        ->where(function ($query) {
            $query->whereMonth('tanggal', '12')
                ->whereYear('tanggal', '2024');
        })
        ->where('id_unit', 24)
        ->whereNotIn('id', $idsToKeep)
        // ->whereNull('status')
        ->delete();

    dd('OK');
});

Route::get('update-validate-by', function () {
    $jadwalService = new JadwalService();

    DB::beginTransaction();
    $jadwals = Jadwal::where(function ($query) {
        $query->whereMonth('tanggal', request('month'))
            ->whereYear('tanggal', request('year'));
    })
        ->whereNull('validate_by')
        // ->where('created_by', request('by'))
        ->chunkById(100, function ($jadwals) use ($jadwalService) {

            foreach ($jadwals as $jadwal) {
                $karyawan = \App\Models\MKaryawan::where('nip', $jadwal->nip)->first();
                $jadwal->update([
                    'validate_at' => now(),
                    'validate_by' =>  $jadwalService->getValidateJadwal($karyawan)
                ]);
            }
        });
    DB::commit();

    dd('OK GAS');
});

// Route::get('update-unit', function () {
//     try {
//         DB::beginTransaction();
//         $jadwals = Jadwal::groupBy('nip')->get();
//         foreach ($jadwals as $jadwal) {
//             $unit = MKaryawan::where('nip', $jadwal->nip)
//                 ->pluck('id_unit')
//                 ->first();
//             if (!is_null($unit)) {
//                 Jadwal::where(function ($query) {
//                     $query->whereMonth('tanggal', 10)
//                         ->whereYear('tanggal', '2024');
//                 })
//                     ->where('nip', $jadwal->nip)->update([
//                         'id_unit' => $unit
//                     ]);
//             }
//         }
//         DB::commit();

//         return 'OK';
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//     }
// });


// Route::get('update-izin-unit', function () {
//     try {
//         DB::beginTransaction();

//         $izins = Izin::all();
//         foreach ($izins as $izin) {
//             $unit = Unit::where('id');
//             $izin->update([
//                 'tanggal' => $izin->izinCuti->mulai
//             ]);
//         }

//         DB::commit();

//         return 'OK';
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//     }
// });

// Route::get('update-acc-tukar', function () {
//     try {
//         DB::beginTransaction();

//         $tukar = TukarJadwal::all();
//         foreach ($tukar as $t) {
//             $t->update([
//                 'acc_sdm' => '222082017',
//                 'acc_at' => $t->updated_at
//             ]);
//         }

//         DB::commit();

//         return 'OK';
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//     }
// });

// Route::get('update-tgl-izin', function () {
//     try {
//         DB::beginTransaction();

//         $izins = Izin::with('izinCuti')
//             ->where('jenis_table', 'izin_cuti')
//             ->get();
//         foreach ($izins as $izin) {
//             $izin->update([
//                 'tanggal' => $izin->izinCuti->mulai
//             ]);
//         }

//         DB::commit();

//         return 'OK';
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//     }
// });

Route::get('/presensi', function () {
    App\Models\Presensi::whereHas('jadwal')->chunkById(100, function ($items) {
        foreach ($items as $item) {
            /**
             * kerja
             */
            $mulai = Carbon\Carbon::make("$item->tanggal $item->masuk");
            $pulang = Carbon\Carbon::make("{$item->jadwal->tanggal} {$item->jadwal->jam_pulang}");
            // $beforePulang = Carbon\Carbon::createFromFormat('Y-m-d H:i', $item->jadwal->tanggal . " {$item->jadwal->jam_pulang}");
            if ($mulai->greaterThan($pulang)) {
                $pulang->addDay();
            }
            $diffKerja = $mulai->diffInMinutes($pulang);
            /**
             * TELAT
             */
            $mulaiTelat = Carbon\Carbon::make("$item->tanggal {$item->jadwal->jam_masuk}")->addMinutes($item->jadwal->telat_masuk);
            $telat = Carbon\Carbon::make("$item->tanggal $item->masuk");
            $diffTelat = 0;
            if ($mulaiTelat->lessThan($telat)) {
                $diffTelat = $mulaiTelat->diffInMinutes($telat);
            }

            App\Models\Presensi::where('id', $item->id)->update([
                'ttl_kerja' => $diffKerja,
                'ttltelat' => $diffTelat,
            ]);
        }
    });
});

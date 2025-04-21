<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\HistoryLemburController;
use App\Http\Controllers\Api\MKaryawanController;
use App\Http\Controllers\Api\MUnitController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LoginOneDeviceController;
use App\Http\Controllers\Api\CompaniesController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HarianController;
use App\Http\Controllers\Api\HistoryIzinController;
use App\Http\Controllers\Api\HistoryJadwalController;
use App\Http\Controllers\Api\HistoryKaryawanController;
use App\Http\Controllers\Api\HistoryPresensiController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\MIzinController;
use App\Http\Controllers\Api\MJenisDokController;
use App\Http\Controllers\Api\MKaryawanDokController;
use App\Http\Controllers\Api\MLemburController;
use App\Http\Controllers\Api\MLokasiController;
use App\Http\Controllers\Api\MShiftController;
use App\Http\Controllers\Api\OnProgressController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RekapIzinController;
use App\Http\Controllers\Api\RekapJadwalController;
use App\Http\Controllers\Api\RekapLemburController;
use App\Http\Controllers\Api\RekapPresensiController;
use App\Http\Controllers\Api\RekapTerlambatController;
use App\Http\Controllers\Api\TolakController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\LemburReqController;
use App\Notifications\FcmUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Authentication route
 *
 **/
Route::get('dashboard-grafik/line/absen', [DashboardController::class, "lineGrafikAbsen"])->name('dashboard.line_grafik_absen');

Route::middleware(['auth:sanctum', 'preventMultipleLogin'])->group(function () {

    /**
     * API DASHBOARD
     */
    Route::get('dashboard/progress-presensi', [DashboardController::class, "progressPresensi"])->name('dashboard.progress_presensi');
    Route::get('dashboard/presensi/harian', [DashboardController::class, "presensiHarian"])
        ->name('dashboard.presensi_harian');
    Route::get('dashboard/table-presensi/harian', [DashboardController::class, "tablePresensiHarian"])
        ->name('dashboard.table_presensi_harian');
    Route::get('dashboard/table-izin/harian', [DashboardController::class, "tableIzinHarian"])
        ->name('dashboard.table_izin_harian');
    Route::get('dashboard/jadwal/harian', [DashboardController::class, "jadwal"])
        ->name('dashboard.jadwal_harian');
    Route::get('dashboard/statistik-hadir', [DashboardController::class, "statistikHadir"])
        ->name('dashboard.statistik_hadir');
    Route::get('dashboard/statistik-divisi', [DashboardController::class, "statistikDivisi"])
        ->name('dashboard.statistik_divisi');
    Route::get('dashboard/statistik-distr', [DashboardController::class, "statistikDistr"])
        ->name('dashboard.statistik_distr');
    Route::get('dashboard/statistik-trend', [DashboardController::class, "statistikTrend"])
        ->name('dashboard.statistik_trend');
    Route::get('dashboard/statistik-gender', [DashboardController::class, "statistikGender"])
        ->name('dashboard.statistik_gender');
    Route::get('dashboard/list-presensi', [DashboardController::class, "listPresensi"])
        ->name('dashboard.list_presensi');

    /**
     * Master Unit Karyawan
     */
    Route::apiResource('m-unit', MUnitController::class)->names('m_unit');
    Route::get('m-unit/all/data', [MUnitController::class, 'data'])->name('m_unit.data');
    Route::post('m-unit/store-or-update', [MUnitController::class, 'storeOrUpdate'])->name('m_unit.store_or_update');

    /**
     * Master Jenis Izin Karyawan
     */
    Route::apiResource('m-izin', MIzinController::class)->names('m_izin');
    Route::get('m-izin/all/data', [MIzinController::class, 'data'])->name('m_izin.data');
    Route::get('m-izin/by-kode/{kode}', [MIzinController::class, 'byKode'])->name('m_izin.by-kode');

    /**
     * Izin Karyawan
     */
    Route::get('izin/selesai/by-nip', [IzinController::class, "selesai"])->name('izin.selesai');
    Route::get('izin/progress/by-nip', [IzinController::class, 'progressByNip'])->name('izin.progres_by_nip');
    Route::post('izin', [IzinController::class, 'store'])->name('izin.store');
    Route::get('izin/all/data', [IzinController::class, 'data'])->name('izin.data');
    Route::get('izin/confirm/by-nip', [IzinController::class, "confirmByNip"])->name('izin.confirm_by_nip');
    Route::put('izin/acc/submit', [IzinController::class, "accSubmit"])->name('izin.acc_submit');
    Route::delete('izin/{id}', [IzinController::class, "destroy"])->name('izin.destroy');
    Route::get('izin/{id}', [IzinController::class, "show"])->name('izin.show');
    Route::put('izin/batal/{id}', [IzinController::class, "batal"])->name('izin.batal');
    Route::get('izin-last/{nip}', [IzinController::class, "lastIzin"])->name('izin.last_izin');
    Route::get('izin-count/need-approval', [IzinController::class, "needApproval"])->name('izin.need-approval');

    /**
     * Master Jenis Lokasi Presensi Karyawan
     */
    Route::apiResource('m-lokasi', MLokasiController::class)->names('m_lokasi');
    Route::get('m-lokasi/all/data', [MLokasiController::class, 'data'])->name('m_lokasi.data');

    /**
     * Master Jenis Lokasi Presensi Karyawan
     */
    Route::apiResource('m-lembur', MLemburController::class)->names('m_lembur');
    Route::get('m-lembur/all/data', [MLemburController::class, 'data'])->name('m_lembur.data');

    /**
     * Master Shift dan Setting Jam Masuk/Pulang Karyawan
     */
    Route::apiResource('m-shift', MShiftController::class)->names('m_shift');
    Route::get('m-shift/all/data', [MShiftController::class, 'data'])->name('m_shift.data');

    /**
     *  MASTER SHIFT
     */
    Route::apiResource('m-shift', MShiftController::class)->names('m_shift');
    /**
     *  Route Penolakan
     */
    Route::put('tolak/form/{id}', [TolakController::class, 'store'])->name('tolak');

    /**
     * Presensi Karyawan
     */
    Route::prefix('presensi/')->namespace('presensi.')->group(function () {
        Route::get('list', [PresensiController::class, 'index'])->name('list');
        Route::get('show/{id}', [PresensiController::class, 'show'])->name('show');
        Route::post('masuk', [PresensiController::class, 'masuk'])->name('masuk');
        Route::post('pulang', [PresensiController::class, 'pulang'])->name('pulang');
        Route::post('', [PresensiController::class, 'store'])->name('store');
        Route::put('{id}', [PresensiController::class, 'update'])->name('update');
        Route::put('telat/{id}', [PresensiController::class, 'alasanTelat'])->name('alasan_telat');
        Route::put('{id}/status-update', [PresensiController::class, 'updateStatus'])->name('update_status');
        Route::get('jadwal-hari-ini', [PresensiController::class, 'jadwalByNip'])->name('jadwalByNip');
        Route::get('check-jadwal', [PresensiController::class, 'checkJadwal'])->name('checkJadwal');
        Route::get('check-absen-masuk', [PresensiController::class, 'checkAbsenMasuk'])->name('checkAbsenMasuk');
        Route::get('check-absen-pulang', [PresensiController::class, 'checkAbsenPulang'])->name('checkAbsenPulang');
        Route::post('validasi-radius', [PresensiController::class, 'radiusValidation'])->name('radius_validation');
    });

    /**
     * Master Wilayah Indonesia
     */
    Route::get('wilayah', [WilayahController::class, 'search'])->name('wilayah.search');

    /**
     * Authentikasi Logout
     */
    Route::delete('logout', [LoginController::class, 'logout'])->name('auth.logout');
    Route::delete('logout-one', [LoginOneDeviceController::class, 'logout'])->name('auth.logout');
    Route::put('change-password', [LoginController::class, 'changePassword'])->name('auth.change_password');

    /**
     *  API JADWAL
     */
    Route::apiResource('jadwal', JadwalController::class)->except('show')->names('jadwal');
    Route::get('jadwal-show/{id}', [JadwalController::class, 'show'])->name('jadwal.show');
    Route::get('jadwal/{nip?}/nip', [JadwalController::class, 'showByNip'])->name('jadwal.show_by_nip');
    Route::put('jadwal/{nip?}/update-presensi', [JadwalController::class, 'updatePresensi'])->name('jadwal.update_presensi');
    Route::get('jadwalku', [JadwalController::class, 'jadwalku'])->name('jadwal.jadwalku');
    Route::get('jadwalku-harian', [JadwalController::class, 'jadwalkuHarian'])->name('jadwal.jadwalku_harian');
    Route::put('update/jadwalku/{id}', [JadwalController::class, 'updateJadwalku'])->name('jadwal.update_jadwalku');
    Route::delete('jadwal/all', [JadwalController::class, 'destroyAll'])->name('jadwal.destroy_all');
    Route::get('jadwal-notif', [JadwalController::class, 'notif'])->name('jadwal.notif');

    /** jadwal/import?jenis=excel */
    Route::post('jadwal/import/', [JadwalController::class, 'import'])->name('jadwal.import');
    Route::get('jadwal-unit', [JadwalController::class, 'jadwalUnit'])->name('jadwal.unit_all');
    Route::get('jadwal-unit-status', [JadwalController::class, 'jadwalUnitStatus'])->name('jadwal.unit_status');
    Route::post('jadwal/approval', [JadwalController::class, 'approval'])->name('jadwal.approval');
    Route::post('kosongkan-jadwal', [JadwalController::class, 'kosongkanJadwal'])->name('jadwal.kosongkan_jadwal');
    Route::get('jadwal-template', [JadwalController::class, 'downloadTemplate'])->name('jadwal.download_template');
    Route::get('jadwal/check-periode', [JadwalController::class, 'checkPeriode'])->name('jadwal.check_periode');
    Route::get('jadwal-show-by-date/{date}', [JadwalController::class, 'showByDate'])->name('jadwal.show_by_date');

    /**
     *  API KARYAWAN
     */
    Route::apiResource('m-karyawan', MKaryawanController::class)->names('m_karyawan');
    Route::get('m-karyawan/all/data', [MKaryawanController::class, 'data'])->name('m_karyawan.data');
    Route::post('m-karyawan/create-user', [MKaryawanController::class, 'createUser'])->name('m_karyawan.create_user');
    Route::get('m-karyawan/by-unit/{id_unit}', [MKaryawanController::class, 'byUnit'])->name('m_karyawan.by_unit');
    Route::get('m-karyawan/sisa/cuti', [MKaryawanController::class, 'sisaCuti'])->name('m_karyawan.sisa_cuti');
    Route::put('m-karyawan/update-cuti/{nip}', [MKaryawanController::class, 'updateCuti'])->name('m_karyawan.update_cuti');
    Route::get('m-karyawan/update-session/{nip}', [MKaryawanController::class, 'updateSession'])->name('m_karyawan.session');
    Route::get('m-karyawan/detail/{nip}', [MKaryawanController::class, 'detail'])->name('m_karyawan.detail');
    Route::delete('m-karyawan/remove-detail/{nip}', [MKaryawanController::class, 'removeDetail'])->name('m_karyawan.remove-detail');
    Route::post('m-karyawan/detail', [MKaryawanController::class, 'storeDetail'])->name('m_karyawan.store_detail');
    Route::put('m-karyawan/resign/{nip}', [MKaryawanController::class, 'resign'])->name('m_karyawan.resign');
    Route::get('m-karyawan/by-nip/{nip}', [MKaryawanController::class, 'byNip'])->name('m_karyawan.by-nip');
    Route::get('m-karyawan-unit', [MKaryawanController::class, 'searchKaryawanUnit'])->name('m_karyawan_unit.search');
    Route::get('m-karyawan-device/req', [MKaryawanController::class, 'getReqDevice'])->name('m_karyawan.req_device');
    Route::put('m-karyawan-device/req/{id}', [MKaryawanController::class, 'updateDevice'])->name('m_karyawan.update_device');
    // Route::post('m-karyawan-device/reset', [MKaryawanController::class, 'resetDevice'])->name('m_karyawan.req_device');
    Route::delete('m-karyawan-device/req/{id}', [MKaryawanController::class, 'destroyReqDevice'])->name('m_karyawan.req_device');
    Route::get('m-karyawan-detail', [MKaryawanController::class, 'sipIndex'])->name('m_karyawan.detail');
    Route::post('m-karyawan-detail', [MKaryawanController::class, 'addDetail'])->name('m_karyawan.add-detail');
    Route::delete('m-karyawan-detail/{id}', [MKaryawanController::class, 'destroyDetail'])->name('m_karyawan.destroy-detail');
    Route::get('m-karyawan-nama', [MKaryawanController::class, 'getNamaKaryawan'])->name('m-karyawan.nama');

    /**
     * API HISTORY
     */
    Route::apiResource('history-presensi', HistoryPresensiController::class)->names('history_presensi');
    Route::get('history-presensi/by-nip', [HistoryPresensiController::class, "showByNip"])->name('history_presensi.show_by_nip');
    Route::get('history-presensi-progress', [HistoryPresensiController::class, "progress"])->name('history_presensi.progress');
    Route::get('history-presensi/search/karyawan', [HistoryPresensiController::class, "searchKaryawan"])->name('history_presensi.search_karyawan');
    Route::get('history-presensi-grafik', [HistoryPresensiController::class, "grafikKehadiran"])->name('history_presensi.grafik_presensi');

    /**
     * API HISTORY JADWAL
     */
    Route::get('history-jadwal', [HistoryJadwalController::class, "index"])->name('history_jadwal.index');
    Route::put('history-jadwal/update/{id}', [HistoryJadwalController::class, "update"])->name('history_jadwal.update');

    /**
     * API HISTORY JADWAL
     */
    Route::get('history-izin', [HistoryIzinController::class, "index"])->name('history_izin.index');
    /**
     * API HISTORY LEMBUR
     */
    Route::get('history-lembur', [HistoryLemburController::class, "index"])->name('history_lembur.index');
    /**
     * API HISTORY KARYAWAN
     */
    Route::get('history-karyawan', [HistoryKaryawanController::class, "index"])->name('history_karyawan.index');
    Route::get('history-karyawan/kinerja', [HistoryKaryawanController::class, "kinerjaStaf"])->name('history_karyawan.kinerja_staf');

    /**
     * API ON-PROGRESS
     */

    Route::get('on-progress', [OnProgressController::class, 'index'])->name('on_progress.index');

    /**
     * API APPROVAL
     */

    Route::get('approval', [ApprovalController::class, 'index'])->name('approval.index');
    /**
     * API PROFIL
     */
    Route::get('my-profil', [ProfileController::class, 'myProfil'])->name('profile.myProfil');
    Route::get('profil/{nip}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profil-jadwal/{nip}', [ProfileController::class, 'jadwal'])->name('profile-jadwal');
    Route::get('profil-jadwal/get-event', [ProfileController::class, 'getEvent'])->name('profile_jadwal.get_event');
    Route::post('profil-change', [ProfileController::class, 'gantiProfil'])->name('profile-jadwal');
    Route::post('profil/fcm-token-update', [ProfileController::class, 'fcmUpdate'])->name('profile.fcm_update');
    Route::get('profil/statistik-presensi/{nip}', [ProfileController::class, 'statistikPresensi'])->name('profile-jadwal');
    Route::get('profil/pelatihan/{nip}', [ProfileController::class, 'getPelatihan'])->name('profile.pelatihan');
    Route::get('profil/izin/{nip}', [ProfileController::class, 'getIzin'])->name('profile.izin');
    Route::get('profil/tukar/{nip}', [ProfileController::class, 'getTukar'])->name('profile.tukar');
    Route::get('profil/presensi/{nip}', [ProfileController::class, 'getPresensi'])->name('profile.presensi');
    Route::get('profil/grafik/statistik/{nip}', [ProfileController::class, "grafikStatistik"])->name('profile.grafik_statistik');
    Route::post('profil/update-avatar/{nip}', [ProfileController::class, "updateAvatar"])->name('profile.update_avatar');

    /**
     * FCM
     */
    Route::get('dashboard/fcm', function () {
        $user = auth()->user();
        // FcmMessage::create()
        // ->setName('test')
        // ->token('token')
        // ->topic('topic')
        // ->condition('condition')
        // ->data(['a' => 'b'])
        // ->custom(['notification' => []]);
        try {
            Notification::sendNow($user, new FcmUserNotification());

            return $user->routeNotificationForFcm();
        } catch (\Throwable $th) {

            return $th;
        }
        // $user->notify(new FcmUserNotification());

        // return $user->currentAccessToken()->fcm_token;
    });
    /**
     * API HARIAN KERJA
     */
    Route::get('harian-kerja', [HarianController::class, "index"])->name('harian_kerja.index');
    // Route::get('history-karyawan/kinerja', [HistoryKaryawanController::class, "kinerjaStaf"])->name('history_karyawan.kinerja_staf');

    /**
     * API Karyawan Dokumen
     */
    Route::apiResource('m-karyawan-dok', MKaryawanDokController::class)->names('karyawan-dok');
    Route::apiResource('m-jenisdok', MJenisDokController::class)->names('jenis-dok');
    Route::apiResource('lembur', LemburReqController::class)->names('lembur');
    Route::put('lembur-update/status/{id}', [LemburReqController::class, "updateStatus"])->name('lembur.update-status');
    Route::get('lembur-count/need-approval', [LemburReqController::class, "needApproval"])->name('lembur.need-approval');
    // Route::get('history-karyawan/kinerja', [HistoryKaryawanController::class, "kinerjaStaf"])->name('history_karyawan.kinerja_staf');

    Route::get('jadwal/template', [JadwalController::class, 'downloadTemplate'])->name('jadwal.download');
    Route::get('izin/bukti/{id}', [IzinController::class, 'bukti'])->name('izin.bukti');
    // Route::get('profil/{nip}', [ProfileController::class, 'getProfil'])->name('profil.get_profil');

    /**
     * KARYAWAN EXPORT
     */
    Route::get('m-karyawan/export', [MKaryawanController::class, 'exportExcel'])->name('m-karyawan.export');

    /**
     * REKAP EXPORT
     */
    Route::get('rekap/karyawan/export', [HistoryKaryawanController::class, 'exportExcel'])->name('rekap.karyawan');
    // Route::get('rekap/presensi-bulanan/export', [RekapPresensiController::class, 'exportPresensiBulanan'])->name('rekap.export_presensi_bulanan');
    Route::get('rekap/presensi/excel', [RekapPresensiController::class, "exportExcel"])->name('rekap.presensi_export');
    Route::get('rekap-bulanan/export', [RekapPresensiController::class, 'rekapPresensiBulanan'])->name('rekap.rekap_bulanan');
    Route::get('rekap/izin/export', [RekapPresensiController::class, 'exportIzinExcel'])->name('rekap.export_izin_excel');
    Route::get('rekap/lembur/excel', [RekapLemburController::class, 'bulananExcel'])->name('rekap.export_lembur_excel');
    Route::get('rekap/terlambat/excel', [RekapTerlambatController::class, 'bulananExcel'])->name('rekap.export_telat_excel');
    Route::get('rekap/izin/excel', [RekapIzinController::class, 'bulananExcel'])->name('rekap.export_izin_excel');
    Route::get('rekap/jadwal/export', [RekapJadwalController::class, 'bulananExcel'])->name('rekap.export_jadwal_excel');
    // Route::get('rekap-kehadiran', [RekapPresensiController::class, 'rekapKehadiran'])->name('rekap.kehadiran');

    /**
     * history
     */
    Route::get('history/presensi-harian/export', [HistoryPresensiController::class, 'exportHarianExcel'])->name('history.export_presensi_harian');

    /**
     * companies
     */
    Route::get('company', [CompaniesController::class, 'index']);
    Route::put('company/{id}', [CompaniesController::class, 'update']);
});
Route::get('rekap-kehadiran', [RekapPresensiController::class, 'rekapKehadiran'])->name('rekap.kehadiran');
/**
 * USER LOGIN ONE DEVICE
 */
Route::post('login-one', [LoginOneDeviceController::class, 'login'])
    // ->middleware('preventSign')
    ->name('auth.login');

/**
 * USER LOGIN FILE
 */
Route::post('login', [LoginOneDeviceController::class, 'login'])->name('auth.login');
// Route::post('login', [LoginController::class, 'login'])->name('auth.login');
Route::put('login/pengajuan-web', [LoginOneDeviceController::class, 'pengajuanDevice'])->name('auth.pengajuan-device');
Route::get('jadwal/template/unit', [JadwalController::class, 'downloadTemplate'])->name('jadwal.template');
Route::get('rekap/karyawan/export', [HistoryKaryawanController::class, 'exportExcel'])->name('rekap.karyawan');

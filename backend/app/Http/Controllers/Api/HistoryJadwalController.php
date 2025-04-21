<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\JadwalGanti;
use App\Models\MShift;
use App\Models\User;
use App\Services\JadwalService;
use App\Utils\CheckRole;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HistoryJadwalController extends Controller
{
    use ApiResponse;

    private JadwalService $jadwalService;

    public function __construct(JadwalService $jadwalService)
    {
        $this->jadwalService = $jadwalService;
    }

    public function index()
    {
        try {
            $explode = explode('to', request('tanggal'));
            $start = Carbon::make($explode[0])->format('Y-m-d');
            $end = Carbon::make($explode[1])->format('Y-m-d');
            $user = auth()->user();
            $idUnit = CheckRole::checkAtasanSemua($user->role) ? request('id_unit') : $user->mKaryawan->id_unit;

            $jadwal = Jadwal::with(['presensi' => function ($query) {
                $query->select('id', 'id_jadwal', 'status');
            }])
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('tanggal', [$start, $end]);
                })
                ->where('id_unit', $idUnit)
                ->orderBy('tanggal')
                ->get([
                    'id',
                    'nama',
                    'id_unit',
                    'libur',
                    'tanggal',
                    'kode_shift',
                    'validate_at',
                    'status'
                ]);

            $periods = CarbonPeriod::create($start, $end);
            $results = [];
            $labels = [];
            $karyawans = $jadwal->groupBy('nama')->keys();

            foreach ($periods as $key => $period) {
                $labels[] = "{$period->dayName}, {$period->format('d')}";
            }
            foreach ($periods as $key => $period) {
                foreach ($karyawans as $karyawan) {
                    $tgl = $period->format('Y-m-d');

                    $jadwalPerTanggal = $jadwal->filter(function ($j) use ($tgl, $karyawan) {
                        return $j['tanggal'] === $tgl && $j['nama'] === $karyawan;
                    });

                    $shifts = [];
                    $now = now();
                    foreach ($jadwalPerTanggal as $shift) {
                        if (count($jadwalPerTanggal) > 0) {
                            $shifts[] = [
                                'id' => $shift->id,
                                'libur' => $shift->libur,
                                'kode_shift' => $shift->kode_shift,
                                'validate_at' => $shift->validate_at,
                                'locked' => !is_null($shift->status) || $now->greaterThan($period),
                                'status' => in_array($shift->status, [1, 2]) ? optional($shift->presensi)->status : $shift->status_cast
                            ];
                        } else {
                            $shifts[] = [
                                'id' => null,
                                'libur' => null,
                                'tanggal' => null,
                                'kode_shift' => null,
                                'validate_at' => null,
                                'locked' => null,
                                'status' => null
                            ];
                        }
                    }

                    $results[$karyawan][$period->format('Y-m-d')] = $shifts;
                }
            }

            $resp = [
                'label' => $labels,
                'rows' => $results
            ];

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $jadwal = Jadwal::find($id);
            $mShift = MShift::where('kode', $request->kode_shift)->first();

            JadwalGanti::create([
                'id_jadwal' => $jadwal->id,
                'old_kode' => $jadwal->kode_shift,
                'new_kode' => $mShift->kode,
                'old_masuk' => $jadwal->jam_masuk,
                'new_masuk' => $mShift->jam_masuk,
                'old_pulang' => $jadwal->jam_pulang,
                'new_pulang' => $mShift->jam_pulang,
                'created_by' => $user->nip,
            ]);

            $jadwal->update([
                'kode_shift' => $mShift->kode,
                'mulai_absen' => $mShift->mulai_absen,
                'jam_masuk' => $mShift->jam_masuk,
                'telat_masuk' => $mShift->telat_masuk,
                'jam_pulang' => $mShift->jam_pulang,
                'telat_pulang' => $mShift->telat_pulang,
                'update_by' => $user->nip,
            ]);
            DB::commit();

            return $this->okApiResponse($jadwal);
        } catch (\Throwable $th) {
            DB::rollBack();

            // return $this->errorApiResponse($th->getTraceAsString());
            return $this->errorApiResponse($th->getMessage());
        }
    }
}

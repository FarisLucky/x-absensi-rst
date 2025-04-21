<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class ApprovalController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $user = auth()->user();

            $params = [
                'user' => $user,
                'year' => request('year'),
                'month' => request('month'),
                'unit' => request('unit'),
            ];
            $jadwals = Jadwal::with('mKaryawan', 'mKaryawan.mUnit')
                ->select('nip', 'nama', 'kode_shift', 'shift', 'jam_masuk', 'jam_pulang', 'tanggal', 'libur')
                ->when($user->role === User::SUPER_ADMIN, function ($query) {
                    $query->whereNotNull('validate_at');
                }, function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->whereNull('validate_at')->where('validate_by', $user->nip);
                    });
                })
                ->when(!is_null($params['month']) || !is_null($params['year']), function ($query) use ($params) {
                    $query->where(function ($query) use ($params) {
                        $query->whereMonth('tanggal', $params['month'])->whereYear('tanggal', $params['year']);
                    });
                }, function ($query) {
                    /**
                     * DEFAULT IS MONTH OR YEAR NULL OR Empty
                     */
                    $query->where(function ($query) {
                        $now = now();
                        $query->whereMonth('tanggal', $now->format('m'))->whereYear('tanggal', $now->format('Y'));
                    });
                })
                ->orderBy('tanggal')
                ->get();
            if ($jadwals->isEmpty()) {
                throw new Exception('Jadwal Kosong, Silahkan Pilih Bulan');
            }

            $resp = [
                "columns" => [],
                "rows" => [],
            ];

            /**
             * SETUP ROWS
             */
            $karyawanUnit = $jadwals->groupBy('nip')->all();
            $jadwalUnit = collect([]);
            foreach ($karyawanUnit as $nip => $karyawan) {
                $jadwal = $jadwals->filter(function ($item) use ($nip) { // JIKA NIP SAMA
                    return $item->nip == strval($nip);
                })
                    ->groupBy(function ($item) { // GROUP BY TANGGAL
                        return Carbon::make($item['tanggal'])->format('d');
                    })
                    ->map(function ($shift) { // PER SHIFT
                        return $shift->map(function ($item) { // FILTER KODE SHIFT
                            return $item->libur == 1 ? 'Libur' : "{$item->shift} \n ({$item->jam_masuk} - {$item->jam_pulang})";
                        })
                            ->implode(', ');
                    });

                $jadwal = collect(["unit" => $karyawan->first()->mKaryawan->mUnit->nama] + $jadwal->all())->sortBy('unit');
                $jadwalUnit->push([
                    "{$nip}-{$karyawan->first()->nama}" =>
                    $jadwal,
                ]);
            }

            $columns = collect([]);
            /**
             * Ambil jadwal staf yang pertama
             */
            $columnsData = collect(
                array_values($jadwalUnit->first())[0]
            );

            $columns->push(["nama"]);
            $columns->push($columnsData->keys()->all());
            $resp["columns"] = $columns->flatten()->all();
            $resp["rows"] = $jadwalUnit->map(function ($item) {
                $newItem = collect(array_values($item)[0]);
                $newItem->prepend(array_keys($item)[0], "nama");

                return $newItem;
            });

            return $this->okApiResponse($resp);
        } catch (\Throwable $th) {

            // return $this->errorApiResponse($th->getTraceAsString());
            return $this->errorApiResponse($th->getMessage());
        }
    }
}

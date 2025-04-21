<?php

namespace App\Http\Controllers\Api;

use App\Exports\RekapKaryawanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\User;
use App\Utils\CheckRole;

class HistoryKaryawanController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $user = auth()->user();
            $month = request('month');
            $year = request('year');
            $search = request('search');
            $unit = request('unit');
            $limitSearch = 10;

            $karyawans = MKaryawan::with([
                'mUnit' => function ($query) {
                    $query->select('id', 'nama');
                }
            ])
                ->select('nip', 'nama', 'id_unit')
                ->withCount([
                    'jadwal as jadwal' => function ($query) use ($month, $year) {
                        $query
                            // ->select('nip')
                            ->where('libur', 0)
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                    'jadwal as alpa' => function ($query) use ($month, $year) {
                        $query
                            // ->select('nip')
                            ->where('status', Jadwal::TIDAK_HADIR)
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                    'presensi as tepat' => function ($query) use ($month, $year) {
                        $query
                            // ->select('nip')
                            ->where('status', 'TEPAT')
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                    'presensi as telat' => function ($query) use ($month, $year) {
                        $query
                            // ->select('nip')
                            ->where('status', 'TELAT')
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                ])
                ->when(in_array($user->role, [User::KASUB, User::KABID]) && is_null($unit), function ($query) use ($user) {
                    $bawahan = MJabatan::with('children')->where('id_parent', $user->jabatans[0]->id_jabatan)->get();

                    $query->whereHas('jabatans', function ($query) use ($bawahan) {
                        $listId = [];
                        /**
                         * FILTER DATA SEMUA BAWAHAN
                         */
                        $childFunc = function ($c, &$listId) use (&$childFunc) {
                            if (is_null($c)) {
                                return;
                            }
                            foreach ($c as $ch) {
                                array_push($listId, $ch->id);
                                if (!is_null($ch->children)) {
                                    $childFunc($ch->children, $listId);
                                }
                            }
                        };
                        $childFunc($bawahan, $listId);
                        $query->whereIn('id_jabatan', $listId);
                    });
                })
                ->when(CheckRole::checkAtasanSemua($user->role) && !is_null($unit), function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->when(in_array($user->role, [User::KEPALA, User::STAFF]), function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                })
                ->when(!is_null($search), function ($query) use ($search, $limitSearch) {
                    $query->where('nama', 'LIKE', "%$search%")
                        ->where('nip', 'LIKE', "%$search%")
                        ->limit($limitSearch);
                })
                ->notResign()
                ->latest()
                ->get();

            return $this->okApiResponse($karyawans);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
    public function kinerjaStaf()
    {
        try {
            $month = request('month');
            $year = request('year');
            $unit = request('unit');
            $search = request('search');
            $perPage = 10;

            $karyawans = MKaryawan::with([
                'mUnit' => function ($query) {
                    $query->select('id', 'nama');
                },
            ])
                ->select('id', 'nip', 'nama', 'id_unit', 'photo')
                ->withCount([
                    'jadwal as hari_kerja' => function ($query) use ($month, $year) {
                        $query
                            ->where('libur', 0)
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                    'jadwal as hadir' => function ($query) use ($month, $year) {
                        $query
                            ->where('status', Jadwal::SELESAI)
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                    'jadwal as alpa' => function ($query) use ($month, $year) {
                        $query
                            ->where('status', Jadwal::TIDAK_HADIR)
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                    'presensi as telat' => function ($query) use ($month, $year) {
                        $query
                            ->where('status', \App\Models\Presensi::TELAT)
                            ->where(function ($query) use ($month, $year) {
                                $query->whereMonth('tanggal', $month)
                                    ->whereYear('tanggal', $year);
                            });
                    },
                ])
                ->when(!is_null($unit), function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('nama', 'LIKE', "%$search%");
                })
                ->notResign()
                ->latest()
                ->paginate($perPage);

            return $this->okApiResponse($karyawans);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function exportExcel()
    {
        try {

            $params = [
                'month' => request('month'),
                'year' => request('year'),
                'unit' => request('unit'),
                'search' => request('search'),
            ];

            return Excel::download(new RekapKaryawanExport($params), "rekap karyawan.xlsx");
        } catch (\Throwable $th) {
            dd($th->getMessage());
            abort(404);
        }
    }
}

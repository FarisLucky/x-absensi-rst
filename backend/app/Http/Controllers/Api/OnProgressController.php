<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Jadwal;
use App\Models\Presensi;

class OnProgressController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $unit = request('unit');
            $search = request('search');
            $status = request('status');

            $onProgress = Jadwal::with([
                'mKaryawan' => function ($query) {
                    $query->select('nip', 'nama', 'sex', 'photo');
                },
            ])
                ->selectIdx()
                ->whereDate('tanggal', now()->format('Y-m-d'))
                ->when(!is_null($unit), function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('nama', 'LIKE', "%{$search}%");
                })
                ->when(!is_null($status), function ($query) use ($status) {
                    switch ($status) {
                        case Jadwal::PRESEN_TEPAT:
                            $query->where('status_absen', Jadwal::PRESEN_TEPAT);
                            break;
                        case Jadwal::PRESEN_TELAT:
                            $query->where('status_absen', Jadwal::PRESEN_TELAT);
                        case 'IZIN':
                            $query->where('status', Jadwal::IZIN);
                            break;
                    }
                })
                ->orderByDesc('status')
                ->orderByDesc('updated_at')
                ->get();

            return $this->okApiResponse(
                $onProgress,
                'Berhasil Menghapus Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

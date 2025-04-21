<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Izin;
use App\Models\User;
use App\Utils\CheckRole;
use Illuminate\Support\Carbon;

class HistoryIzinController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $user = auth()->user();
            $range = request('range_tanggal');
            $perPage = request('perPage');
            $unit = request('unit');
            $izin = request('izin');
            $search = request('search');

            $izins = Izin::with([
                'pemohon' => function ($query) {
                    $query->select('nip', 'id_unit', 'photo');
                },
                'izinBukti',
            ])
                ->selectIdx()
                ->when(!is_null($range), function ($query) use ($range) {
                    $tgl = explode('to', $range);
                    $start = Carbon::make($tgl[0])->format('Y-m-d');
                    $end = Carbon::make($tgl[1])->format('Y-m-d');
                    $query->whereHas('izinDetail', function ($query) use ($start, $end) {
                        $query->whereBetween('tanggal', [$start, $end]);
                    });
                })
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('nip', 'LIKE', "%$search%")
                        ->orWhere('nama', 'LIKE', "%$search%");
                })
                ->when(!is_null($izin), function ($query) use ($izin) {
                    $query->where('kode_izin', $izin);
                })
                ->when($user->role !== User::SUPER_ADMIN, function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                })
                ->when(!is_null($unit), function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->latest()
                ->paginate($perPage);

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\LemburReq;
use App\Models\User;
use Illuminate\Support\Carbon;

class HistoryLemburController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $user = auth()->user();
            $range = request('range_tanggal');
            $perPage = request('perPage');
            $unit = request('unit');
            $search = request('search');

            $lemburs = LemburReq::with([
                'pemohon' => function ($query) {
                    $query->select('nip', 'photo');
                }
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
                ->when($user->role !== User::SUPER_ADMIN, function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                })
                ->when(!is_null($unit), function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->latest()
                ->paginate($perPage);

            return $this->okApiResponse(
                $lemburs,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

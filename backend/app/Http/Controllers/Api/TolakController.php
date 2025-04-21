<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Izin;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\Tolak;
use App\Models\TukarJadwal;
use Illuminate\Http\Request;

class TolakController extends Controller
{
    use ApiResponse;

    public function store(Request $request, $id)
    {
        try {

            $relation = null;
            $tolak = null;

            if ($request->jenis === Tolak::IZIN) {
                $relation = Izin::find($id);
                $tolak = Izin::DITOLAK;
            }

            $payload = [
                'id_relation' => $relation->id,
                'ket' =>  $request->ket,
                'jenis' => strtoupper($request->jenis),
                'created_by' => auth()->user()->nip,
            ];

            Tolak::create($payload);

            $relation->update([
                'acc_status' => $tolak,
                'acc_at' => now()
            ]);

            return $this->okApiResponse(
                'OK',
                'Berhasil Menghapus Data'
            );
        } catch (\Throwable $th) {

            // return $this->errorApiResponse($th->getTraceAsString());
            return $this->errorApiResponse($th->getMessage());
        }
    }
}

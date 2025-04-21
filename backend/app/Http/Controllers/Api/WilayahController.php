<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    use ApiResponse;

    public function search()
    {
        try {

            $length = request('length');
            $kode = request('kode');
            $end = request('end');

            $wilayahs = DB::table('wilayah');

            $wilayahs->when($length != '', function ($query) use ($length) {
                $query->whereRaw("length(KODE) = {$length}");
            });

            $wilayahs->when($kode != '', function ($query) use ($kode, $end) {
                $query->whereRaw("substring(KODE, 1, {$end}) = '{$kode}'");
            });

            // return $this->successApiResponse($wilayahs->toSql());
            return $this->successApiResponse($wilayahs->get());
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}

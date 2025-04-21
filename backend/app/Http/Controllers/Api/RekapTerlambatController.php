<?php

namespace App\Http\Controllers\Api;

use App\Exports\TerlambatBulananExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class RekapTerlambatController extends Controller
{
    public function bulananExcel()
    {
        try {

            $tgl = request('range_tanggal');
            $unit = request('unit');
            $now = now()->format('d-m-Y');

            $splitTgl = explode('to', $tgl);
            $start = trim($splitTgl[0]);
            $end = trim($splitTgl[1]);

            $telat = new TerlambatBulananExport();
            $telat->start = $start;
            $telat->end = $end;
            $telat->idUnit = $unit;

            return Excel::download($telat, "Terlambat export {$now}.xlsx");
        } catch (\Throwable $th) {

            // dd($th->getTraceAsString());
            abort(500, $th->getMessage());
        }
    }
}

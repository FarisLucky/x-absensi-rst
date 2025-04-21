<?php

namespace App\Http\Controllers\Api;

use App\Exports\RekapJadwalExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class RekapJadwalController extends Controller
{
    public function bulananExcel()
    {
        try {

            $range = request('range');
            $idUnit = request('unit');
            $now = now()->format('d-m-Y');

            $splitTgl = explode('to', $range);
            if(count($splitTgl) > 1) {
                $start = trim($splitTgl[0]);
                $end = trim($splitTgl[1]);
            } else {
                $start = $range;
                $end = $range;
            }

            $telat = new RekapJadwalExport([
                'start' => $start,
                'end' => $end,
                'unit' => $idUnit,
            ]);

            return Excel::download($telat, "Jadwal export {$now}.xlsx");
        } catch (\Throwable $th) {

            dd($th->getMessage());
            // dd($th->getTraceAsString());
            // abort(500, $th->getMessage());
        }
    }
}

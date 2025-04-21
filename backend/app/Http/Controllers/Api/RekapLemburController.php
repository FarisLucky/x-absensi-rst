<?php

namespace App\Http\Controllers\Api;

use App\Exports\LemburBulananExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class RekapLemburController extends Controller
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

            $params = [
                'start' => $start,
                'end' => $end,
                'id_unit' => $unit,
            ];
            // dd($params);

            $lembur = new LemburBulananExport();
            $lembur->start = $start;
            $lembur->end = $end;

            return Excel::download($lembur, "lembur export {$now}.xlsx");
        } catch (\Throwable $th) {

            abort(500, $th->getMessage());
        }
    }
}

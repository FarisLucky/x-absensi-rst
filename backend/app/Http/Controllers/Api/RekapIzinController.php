<?php

namespace App\Http\Controllers\Api;

use App\Exports\IzinBulananExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class RekapIzinController extends Controller
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

            $izin = new IzinBulananExport();
            $izin->start = $start;
            $izin->end = $end;
            $izin->idUnit = $unit;

            return Excel::download($izin, "Izin export {$now}.xlsx");
        } catch (\Throwable $th) {

            abort(500, $th->getTraceAsString());
        }
    }
}

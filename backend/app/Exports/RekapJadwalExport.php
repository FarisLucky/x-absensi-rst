<?php

namespace App\Exports;

use App\Exports\Sheets\JadwalSheet;
use App\Models\MUnit;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapJadwalExport implements WithMultipleSheets
{
    use Exportable;

    private $params = null;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function sheets(): array
    {
        $result = [];

        $units = MUnit::whereHas('jadwal')
            ->when(!is_null($this->params['unit']), function ($query) {

                $all = false;
                foreach ($this->params['unit'] as $unit) {
                    if ($unit === -1) {
                        $all = true;
                        break;
                    }
                }

                if (!$all) {
                    $query->whereIn('id', $this->params['unit']);
                }
            }, function($query){
                $query->whereHas('jadwal');
            })
            ->get();

        if (!is_null($units)) {
            foreach ($units as $unit) {
                $jadwalSheet = new JadwalSheet();
                $jadwalSheet->start = $this->params['start'];
                $jadwalSheet->end = $this->params['end'];
                $jadwalSheet->idUnit = $unit->id;
                $jadwalSheet->unitName = $unit->nama;

                $result[$unit->nama] = $jadwalSheet;
                // dd($this->params['start']);
            }
        }

        return $result;
    }
}

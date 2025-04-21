<?php

namespace App\Exports;

use App\Exports\Sheets\LemburSheet;
use App\Models\MUnit;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LemburExport implements WithMultipleSheets
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

        $units = MUnit::when(!is_null($this->params['id_unit']), function ($query) {
            $query->whereIn('id', $this->params['id_unit']);
        })
            ->get();

        if (!is_null($units)) {
            foreach ($units as $unit) {
                $lemburSheet = new LemburSheet();
                $lemburSheet->start = $this->params['start'];
                $lemburSheet->end = $this->params['end'];
                $lemburSheet->idUnit = $unit->id;
                $lemburSheet->unitName = $unit->nama;

                $result[$unit->nama] = $lemburSheet;
                // dd($this->params['start']);
            }
        }

        return $result;
    }
}

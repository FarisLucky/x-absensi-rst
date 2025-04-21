<?php

namespace App\Exports;

use App\Models\MUnit;
use App\Exports\Sheets\PresensiBulananSheet;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapPresensiBulananExport implements WithMultipleSheets
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
        $range = $this->params['range'];

        if (!is_null($range)) {
            $splitRange = explode('to', $range);
            if ($this->params['jenis'] === 'BULANAN') {
                if (count($splitRange) > 1) {
                    $periods = new CarbonPeriod(trim($splitRange[0]), '1 month', trim($splitRange[1]));
                } else {
                    $periods = new CarbonPeriod(trim($splitRange[0]), '1 month', trim($splitRange[0]));
                }
            } else {
                if (count($splitRange) > 1) {
                    $periods = new CarbonPeriod(trim($splitRange[0]), '1 year', trim($splitRange[1]));
                } else {
                    $periods = new CarbonPeriod(trim($splitRange[0]), '1 year', trim($splitRange[0]));
                }
            }

            foreach ($periods as $periode) {
                if ($this->params['jenis'] === 'BULANAN') {
                    $formatPeriode = $periode->format('m-Y');
                } else {
                    $formatPeriode = $periode->format('Y');
                }
                $presensiSheet = new PresensiBulananSheet([
                    'range' => $formatPeriode,
                    'unit' => $this->params['unit'],
                    'jenis' => $this->params['jenis'],
                ]);
                $result[$formatPeriode] = $presensiSheet;
            }
        }

        return $result;
    }
}

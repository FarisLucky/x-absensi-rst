<?php

namespace App\Exports;

use App\Services\LemburService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LemburBulananExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow, WithTitle
{
    public $start;
    public $end;
    public $idUnit;
    public $unitName;

    public function view(): View
    {
        // dd('test');
        $start = Carbon::make($this->start);
        $end = Carbon::make($this->end);
        $params = [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'id_unit' => $this->idUnit,
        ];

        $lemburService = new LemburService();
        $lemburs = $lemburService->findByDateRangeAndUnit($params);
        // dd($lemburs);

        return view('lembur_export', compact('lemburs'), $params);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('PT Catur Karsa Inkrisuba');
        $drawing->setDescription('PT Catur Karsa Inkrisuba');
        $drawing->setPath(public_path('/logo_new.png'));
        $drawing->setWidth(90);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function styles(Worksheet $sheet)
    {

        return [
            'B1'  => ['font' => ['size' => 18, 'bold' => true]],
            'B2'  => ['font' => ['size' => 20, 'bold' => true]],
            'B3'  => ['font' => ['size' => 14]],
            'A7:Z7'  => ['font' => ['size' => 12, 'bold' => true]],
        ];
    }

    public function headingRow(): int
    {
        return 6;
    }
    public function startRow(): int
    {
        return 6;
    }
    public function title(): string
    {
        return "Rekap Lembur";
    }
}

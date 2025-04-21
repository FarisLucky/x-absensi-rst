<?php

namespace App\Exports\Sheets;

use App\Models\MKaryawan;
use App\Models\MUnit;
use App\Services\RekapPresensiService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiBulananSheet implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow, WithTitle
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function view(): View
    {
        $range = $this->params['range'];
        $unit = $this->params['unit'];
        $jenis = $this->params['jenis'];
        $start = null;
        $end = null;

        $all = false;
        foreach ($this->params['unit'] as $unit) {
            if (intval($unit) === -1) {
                $all = true;
                break;
            }
        }

        $units = MUnit::with([
            'karyawan' => function ($query) {
                $query->select('id', 'nip', 'nama', 'id_unit')->notResign();
            }
        ])
            ->whereHas('jadwal')
            ->when(count($this->params['unit']) > 0 && !$all, function ($query) {
                $query->whereIn('id', $this->params['unit']);
            })
            ->get([
                'id',
                'nama'
            ]);

        $rekapPresensiService = new RekapPresensiService();
        $data = [];
        foreach ($units as $unit) {
            $data[] = $rekapPresensiService->ttlPresensi(
                [
                    'range' => $range,
                    'unit' => $unit,
                    'jenis' => $jenis,
                ],
                $unit->karyawan
            );
        }

        return view('rekap_presensi_bulanan_export', compact('data', 'start', 'end'));
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
            'A5'  => ['font' => ['size' => 13, 'bold' => true]],
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
        return $this->params['range'];
    }
}

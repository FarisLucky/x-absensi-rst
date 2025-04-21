<?php

namespace App\Exports;

use App\Models\MKaryawan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MKaryawanExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function view(): View
    {

        $search = $this->params['search'];
        $unit = $this->params['unit'];

        $karyawans = MKaryawan::with([
            'jabatans' => function ($query) {
                $query->select('id', 'id_jabatan', 'nip');
            },
            'mUnit' => function ($query) {
                $query->select('id', 'nama');
            },
        ])
            ->selectIdx()
            ->get();

        return view('m_karyawan_export', compact('karyawans'));
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
            'A6:Z6'  => ['font' => ['size' => 12, 'bold' => true]],
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
}

<?php

namespace App\Exports;

use App\Models\Izin;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IzinExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function view(): View
    {

        $rangeTanggal = $this->params['range_tanggal'];
        $search = $this->params['search'];
        $izin = $this->params['izin'];
        $unit = $this->params['unit'];

        $izins = Izin::with([
            'pemohon' => function ($query) {
                $query->select('nip', 'nama', 'id_unit');
            },
            'izinBukti'
        ])
            ->selectIdx()
            ->when(!is_null($rangeTanggal), function ($query) use ($rangeTanggal) {
                $query->where(function ($query) use ($rangeTanggal) {
                    $tgl = explode('to', $rangeTanggal);
                    $start = Carbon::make($tgl[0])->format('Y-m-d');
                    $end = Carbon::make($tgl[1])->format('Y-m-d');
                    $query->whereBetween('tanggal', [$start, $end]);
                });
            })
            ->when(!is_null($unit), function ($query) use ($unit) {
                $query->whereHas('pemohon.munit', function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                });
            })
            ->when(!is_null($search), function ($query) use ($search) {
                $query->where('nip', 'LIKE', "%$search%")
                    ->orWhere('nama', 'LIKE', "%$search%");
            })
            ->when(!is_null($izin), function ($query) use ($izin) {
                $query->where('kode_izin', $izin);
            })
            ->whereNotNull('acc_status')
            ->get();

        return view('izin_export', array_merge(compact('izins'), [
            'range_tanggal' => $rangeTanggal,
            'search' => $search,
            'izin' => $izin,
            'unit' => $unit,
        ]));
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
}

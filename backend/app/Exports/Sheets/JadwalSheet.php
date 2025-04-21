<?php

namespace App\Exports\Sheets;

use App\Models\Jadwal;
use App\Services\LemburService;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalSheet implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow, WithTitle
{
    public $start;
    public $end;
    public $idUnit;
    public $unitName;

    public function view(): View
    {
        $start = Carbon::createFromFormat('Y-m', $this->start);
        $end = Carbon::createFromFormat('Y-m', $this->end);
        $params = [
            'start' => $start->startOfMonth(),
            'end' => $end->endOfMonth(),
            'id_unit' => $this->idUnit,
        ];
        $unitName = $this->unitName;

        $jadwals = Jadwal::with([
            'presensi' => function ($query) {
                $query->select('id', 'id_jadwal', 'status');
            }
        ])
            ->select([
                'id',
                'nama',
                'id_unit',
                'libur',
                'tanggal',
                'kode_shift',
                'status',
                DB::raw('CONCAT(YEAR(tanggal), "-", MONTH(tanggal)) as month_val')
            ])
            ->where(function ($query) use ($params) {
                $query->whereBetween('tanggal', [
                    $params['start']->toDateString(),
                    $params['end']->toDateString(),
                ]);
            })
            ->where('id_unit', $params['id_unit'])
            ->orderBy('tanggal')
            ->get();

        $results = $jadwals
            ->groupBy('month_val')
            ->map(function ($item, $key) {
                $periodeTgl = CarbonPeriod::create(
                    Carbon::createFromFormat('Y-m', $key)->startOfMonth()->toDateString(),
                    Carbon::createFromFormat('Y-m', $key)->endOfMonth()->toDateString(),
                );

                $labels = [];
                foreach ($periodeTgl as $period) {
                    $labels[] = "{$period->dayName}, {$period->format('d-m-Y')}";
                }

                return [
                    'label' => $labels,
                    'row' => $item->groupBy('nama')->map(function ($jadwal) {
                        // $jadwal
                        $jadwal->groupBy('tanggal');

                        return $jadwal;
                    }),
                ];
            });

        return view('rekapjadwal_export', compact('results', 'unitName'));
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
        return $this->unitName;
    }
}

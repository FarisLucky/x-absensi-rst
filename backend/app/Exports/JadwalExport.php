<?php

namespace App\Exports;

use App\Models\MKaryawan;
use App\Models\MShift;
use App\Models\MUnit;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalExport implements FromQuery, WithDrawings, ShouldAutoSize, WithMapping, WithHeadings, WithCustomStartCell, WithStyles, WithColumnWidths, WithEvents
{
    private array $idUnit;
    private string $year;
    private string $month;

    public function __construct(array $params)
    {
        $this->idUnit = $params['idUnit'];
        $this->year = $params['year'];
        $this->month = $params['month'];
    }

    public function query()
    {
        return MKaryawan::whereIn('id_unit', $this->idUnit)->notResign()->orderBy('id_unit');
    }
    public function map($karyawan): array
    {
        return [
            $karyawan->nip,
            $karyawan->nama,
        ];
    }

    public function headings(): array
    {
        $headers = [
            'Nip',
            'Nama',
        ];
        $start = Carbon::create($this->year, $this->month)->startOfMonth()->format('Y-m-d'); //returns 2020-03-01
        $end = Carbon::create($this->year, $this->month)->lastOfMonth()->format('Y-m-d');
        $periods = CarbonPeriod::create($start, $end);
        foreach ($periods as $period) {
            $headers[] = $period->isoFormat('D, dddd');
        }

        return $headers;
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /**
                 * ADD HEADER TITLE
                 */
                $units = MUnit::whereIn('id', $this->idUnit)->get(['nama']);
                $unitsName = "";
                foreach ($units as $unit) {
                    $unitsName .= "{$unit->nama}, ";
                }
                $event->sheet
                    ->setCellValue('B1', 'Jadwal Presensi')
                    ->setCellValue('B2', 'PT Catur Karsa Inkrisuba')
                    ->setCellValue('B3', 'Dusun Krajan 2, Randumerak, Kec. Paiton, Kabupaten Probolinggo, Jawa Timur 67291')
                    ->setCellValue('A7', "Bulan: {$this->month} - {$this->year}")
                    ->setCellValue('B7', "Unit: {$unitsName}");
                /**
                 * ADD MASTER SHIFT
                 */
                $shifts = MShift::selectIdx()->get();
                /**
                 * start row
                 */
                $row = 10;
                $event->sheet->getCell('AH' . $row)->setValue('MASTER SHIFT')
                    ->getStyle()
                    ->getFont()
                    ->setBold(true);
                $row++;
                $event->sheet->getCell('AH' . $row)->setValue('(L) LIBUR = 0');
                $row++;

                foreach ($shifts as $shift) {
                    $event->sheet->getCell('AH' . $row)->setValue(
                        "({$shift->kode}) {$shift->nama} = {$shift->jam_masuk} - {$shift->jam_pulang}"
                    );
                    $event->sheet->getStyle('AH' . $row)->getAlignment()->setWrapText(true);

                    /**
                     * ADD ROW
                     */
                    $row = $event->sheet->getHighestDataRow('AH') + 1;
                }
            }
        ];
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A8:AG8')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ]);

        return [
            'B1'  => ['font' => ['size' => 18, 'bold' => true]],
            'B2'  => ['font' => ['size' => 20, 'bold' => true]],
            8  => [
                'font' => ['size' => 12, 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 45,
            'AH' => 40,
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\MKaryawan;
use App\Models\TukarJadwal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapKaryawanExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function view(): View
    {

        $month = $this->params['month'];
        $year = $this->params['year'];
        $unit = $this->params['unit'];
        $search = $this->params['search'];
        $limitSearch = 10;

        $karyawans = MKaryawan::with([
            'mUnit' => function ($query) {
                $query->select('id', 'nama');
            }
        ])
            ->select('nip', 'nama', 'id_unit')
            ->withCount([
                'jadwal as jadwal' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip')
                        ->where('libur', 0)
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                },
                'jadwal as alpa' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip')
                        ->where('status', Jadwal::TIDAK_HADIR)
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                },
                'presensi as tepat' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip')
                        ->where('status', 'TEPAT')
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                },
                'presensi as telat' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip')
                        ->where('status', 'TELAT')
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                },
                'izin as izin' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip')
                        ->where('kode_izin', '<>', Izin::CUTI)
                        ->where('acc_status', Izin::SELESAI)
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                },
                'izin as cuti' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip')
                        ->where('kode_izin', Izin::CUTI)
                        ->where('acc_status', Izin::SELESAI)
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                },
                'tukarJadwal as tukar_jadwal' => function ($query) use ($month, $year) {
                    $query
                        // ->select('nip_pihak1')
                        ->where('acc_status', TukarJadwal::STATUS_OK)
                        ->where(function ($query) use ($month, $year) {
                            $query->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        });
                }
            ]);

        $karyawans->when(!is_null($unit), function ($query) use ($unit) {
            $query->where('id_unit', $unit);
        });

        $karyawans->when(!is_null($search), function ($query) use ($search, $limitSearch) {
            $query->where('nama', 'LIKE', "%$search%")->limit($limitSearch);
        });

        $karyawans = $karyawans->get();

        return view('rekap_karyawan_export', compact('karyawans', 'month', 'year', 'unit'));
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

<?php

namespace App\Exports;

use App\Models\Jadwal;
use App\Models\TukarJadwal;
use App\Models\User;
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

class PresensiHarianExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize, WithHeadingRow, WithStartRow
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $start = now()->format('Y-m-d');
        $end = now()->format('Y-m-d');
        $unit = $this->data['unit'];
        $user = $this->data['user'];
        $range = $this->data['range'];
        $search = $this->data['search'];
        $status = $this->data['status'];

        if (!is_null($range)) {
            $tgl = explode('to', $this->data['range']);
            $start = Carbon::make($tgl[0])->format('Y-m-d');
            $end = Carbon::make($tgl[1])->format('Y-m-d');
        }

        $presensies = Jadwal::leftJoin('presensi', 'presensi.id_jadwal', '=', 'jadwal.id')
            ->leftJoin('presensi_terlambat', 'presensi.id', '=', 'presensi_terlambat.id_presensi')
            ->leftJoin('m_karyawan', 'm_karyawan.nip', '=', 'jadwal.nip')
            ->leftJoin('m_unit', 'm_unit.id', '=', 'm_karyawan.id_unit')
            ->leftJoin('izin_detail', function ($query) {
                $query->on('izin_detail.tanggal', '=', 'jadwal.tanggal');
                $query->on('izin_detail.nip', '=', 'jadwal.nip');
            })
            ->leftJoin('izin', function ($query) {
                $query->on('izin_detail.id_izin', '=', 'izin.id');
            })
            ->leftJoin('tukar_jadwal', function ($query) {
                $query->on('tukar_jadwal.tgl_pihak1', '=', 'jadwal.tanggal');
                $query->on('tukar_jadwal.nip_pihak1', '=', 'jadwal.nip');
                $query->where('tukar_jadwal.jenis', TukarJadwal::TUKAR_OFF_1);
                $query->where('tukar_jadwal.acc_status', TukarJadwal::ACC);
            })
            ->when(!is_null($range), function ($query) use ($start, $end) {
                $query->whereBetween('jadwal.tanggal', [$start, $end]);
            })
            ->when(!is_null($unit), function ($query) use ($unit) {
                $query->whereHas('mKaryawan', function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                });
            })
            ->when($user->role === User::KEPALA, function ($query) use ($user) {
                $query->whereHas('mKaryawan', function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                });
            })
            ->when(!is_null($search), function ($query) use ($search) {
                $query->where('jadwal.nama', 'LIKE', "%{$search}%")
                    ->orWhere('jadwal.nip', 'LIKE', "%{$search}%");
            })
            ->when(!is_null($status), function ($query) use ($status) {
                if (in_array($status, ['TELAT', 'TEPAT'])) {
                    $query->whereHas('presensi', function ($query) use ($status) {
                        $query->where('status', strtoupper($status));
                    });
                }
                if (in_array($status, ['TIDAK HADIR'])) {
                    $query->where('jadwal.status', Jadwal::TIDAK_HADIR);
                }
            })
            ->whereNull('m_karyawan.tgl_resign')
            ->selectRaw('jadwal.id, jadwal.tanggal, jadwal.nip, jadwal.kode_shift, m_karyawan.nama, jadwal.shift, jadwal.jam_masuk, jadwal.jam_pulang, presensi.masuk, presensi.pulang, presensi.status as presensi_status, m_unit.nama as nama_unit, jadwal.status as jadwal_status, izin_detail.tanggal as izin_detail_tanggal, jadwal.libur, izin.izin, tukar_jadwal.jenis as jenis_tukar, presensi_terlambat.ket as ket_terlambat')
            ->orderBy('m_unit.nama')
            ->orderBy('jadwal.tanggal')
            ->orderBy('jadwal.kode_shift')
            ->get();

        return view('presensi_harian_export', compact('presensies', 'start', 'end'));
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
        // $wizardFactory = new Wizard('J1:J300');
        // $wizard = $wizardFactory->newRule(Wizard::CELL_VALUE);
        // $wizard->equals("")->set;

        // $sheet->getStyle('J1:J300')
        // // ->setConditionalStyles([$conditional])
        // ->getFill()
        // ->getStartColor()
        // ->setBac(Color::COLOR_RED);

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

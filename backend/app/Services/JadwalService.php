<?php

namespace App\Services;

use App\Exports\JadwalExport;
use App\Imports\JadwalImport;
use App\Models\Jadwal;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\MShift;
use App\Models\Presensi;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class JadwalService
{
    public function index(array $params)
    {
        $presensies = Presensi::where('nip', $params['user']->nip);
        $filterStatus = $params['filter']['status'];
        $filterBln = $params['filter']['bulan'];
        $filterThn = $params['filter']['tahun'];
        /**
         * FILTER STATUS
         */
        $presensies->when(!is_null($filterStatus), function ($query) use ($filterStatus) {
            $query->where('status', $filterStatus);
        });

        /**
         * FILTER BULAN
         */
        $presensies->when(!is_null($filterBln), function ($query) use ($filterBln) {
            $query->whereMonth('tanggal', $filterBln);
        });

        /**
         * FILTER TAHUN
         */
        $presensies->when(!is_null($filterThn), function ($query) use ($filterThn) {
            $query->whereYear('tanggal', $filterThn);
        });

        return $presensies->get();
    }

    public function store(array $params)
    {
        $input = $params['request'];
        $user = $params['user'];
        $karyawan = MKaryawan::where('nip', $input['nip'])->firstOrFail();
        if (is_null(optional($input)['kode_shift'])) {
            $input['nip'] = $karyawan->nip;
            $input['nama'] = $karyawan->nama;
            $input['id_unit'] = $karyawan->id_unit;
            $input['created_by'] = $user->nip;
        } else {

            $tgl = Carbon::make($input['tanggal'])->format('D');
            $shift = MShift::where('kode', $input['kode_shift'])->firstOrFail();
            $input['nip'] = $karyawan->nip;
            $input['nama'] = $karyawan->nama;
            $input['id_unit'] = $karyawan->id_unit;
            $input['shift'] = $shift->nama;
            $input['mulai_absen'] = $shift->mulai_absen;
            $input['jam_masuk'] = $shift->jam_masuk;
            $input['telat_masuk'] = $shift->telat_masuk;
            $input['jam_pulang'] = $shift->jam_pulang;
            $input['telat_pulang'] = $shift->telat_pulang;
            $input['created_by'] = $user->nip;
            $input['status'] = null;
            $input['libur'] = $tgl === 'Sun' ? 1 : 0;

            if ($tgl === 'Sun') {
                $input = $this->setPayloadLibur($input);
            }
        }

        /**
         * SET KABID
         */
        $input['validate_at'] = $this->setBySdm();

        return Jadwal::create($input);
    }

    public function storeWithRange(array $params)
    {
        $request = $params['request'];
        $user = $params['user'];
        $karyawan = MKaryawan::where('nip', $request['nip'])->firstOrFail();
        $explode = explode('to', $request['tanggal']);
        $startDay = Carbon::parse($explode[0]);
        $endDay = Carbon::parse($explode[1]);
        $period = CarbonPeriod::create($startDay, '1 day', $endDay);
        $now = now();

        try {
            DB::beginTransaction();

            foreach ($period as $day) {
                if ($now->greaterThan($day)) {
                    continue;
                }

                $tgl = $day->format('Y-m-d');

                $shift = MShift::where('kode', $request['kode_shift'])->first();
                $getJadwal = Jadwal::where([
                    'tanggal' => $tgl,
                    'nip' => $karyawan->nip,
                    'kode_shift' => $request['kode_shift'],
                ])->first();

                if (!is_null($getJadwal)) {
                    $getJadwal->update([
                        'kode_shift' => $shift->kode,
                        'shift' => $shift->nama,
                        'mulai_absen' => $shift->mulai_absen,
                        'id_unit' => $karyawan->id_unit,
                        'jam_masuk' => $shift->jam_masuk,
                        'telat_masuk' => $shift->telat_masuk,
                        'jam_pulang' => $shift->jam_pulang,
                        'telat_pulang' => $shift->telat_pulang,
                        'libur' => $day->format('D') === 'Sun' ? 1 : 0,
                    ]);
                } else {
                    $libur = $day->format('D') === 'Sun' ? 1 : 0;
                    $payload = [
                        'nip' => $karyawan->nip,
                        'tanggal' => $tgl,
                        'nama' => $karyawan->nama,
                        'id_unit' => $karyawan->id_unit,
                        'kode_shift' => $shift->kode,
                        'shift' => $shift->nama,
                        'mulai_absen' => $shift->mulai_absen,
                        'jam_masuk' => $shift->jam_masuk,
                        'telat_masuk' => $shift->telat_masuk,
                        'jam_pulang' => $shift->jam_pulang,
                        'telat_pulang' => $shift->telat_pulang,
                        'created_by' => $user->nip,
                        'libur' => $libur,
                        'status' => null
                    ];
                    if ($day->format('D') === 'Sun') {
                        $payload = $this->setPayloadLibur($payload);
                    }

                    $payload['validate_at'] = $this->setBySdm();
                    Jadwal::create($payload);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return true;
    }

    public function update(array $params, $id)
    {
        $request = $params['request'];
        $user = $params['user'];
        $shift = MShift::where('kode', $request['kode_shift'])->firstOrFail();
        $jadwal = Jadwal::find($id);
        if (is_null($jadwal)) {
            throw new Exception('Jadwal tidak ditemukan');
        }

        $payload = [
            'kode_shift' => $request['kode_shift'],
            'shift' => $shift->nama,
            'mulai_absen' => $shift->mulai_absen,
            'jam_masuk' => $shift->jam_masuk,
            'telat_masuk' => $shift->telat_masuk,
            'jam_pulang' => $shift->jam_pulang,
            'telat_pulang' => $shift->telat_pulang,
        ];

        $jadwal->update($payload);

        return $jadwal;
    }

    public function setBySdm()
    {
        return now();
    }

    public function updateLibur(array $params, $id)
    {
        $request = $params['request'];
        $user = $params['user'];
        $jadwal = Jadwal::find($id);
        if (is_null($jadwal)) {
            throw new Exception('Jadwal tidak ditemukan');
        }
        $input = [
            'libur' =>  $request['libur'] ? 1 : 0
        ];

        if ($request['libur']) {
            $input = array_merge($input, [
                'kode_shift' => null,
                'shift' => null,
                'mulai_absen' => null,
                'jam_masuk' => null,
                'telat_masuk' => null,
                'jam_pulang' => null,
                'telat_pulang' => null,
                'update_by' => $user->nip,
            ]);
        }

        $jadwal->update($input);

        return $input;
    }

    public function setPayloadLibur($payload)
    {
        return array_merge($payload, [
            'kode_shift' => null,
            'shift' => null,
            'mulai_absen' => null,
            'jam_masuk' => null,
            'telat_masuk' => null,
            'jam_pulang' => null,
            'telat_pulang' => null,
        ]);
    }

    public function importExcel(array $params)
    {
        return Excel::import(new JadwalImport($params), $params['file']);
    }

    public function download(array $params)
    {
        $name = 'template-' . $params['year'] . '-' . $params['month'] . '.xlsx';

        return Excel::download(new JadwalExport($params), $name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function jadwalUnit(array $params)
    {
        $karyawanUnit = MKaryawan::select('nip', 'nama')
            ->where('id_unit', $params['unit'])
            ->notResign()
            ->get();

        if ($karyawanUnit->isEmpty()) {
            throw new Exception('Unit tidak memiliki Karyawan');
        }

        $jadwals = Jadwal::select('nip', 'kode_shift', 'shift', 'jam_masuk', 'jam_pulang', 'tanggal', 'libur')
            ->whereIn('nip', $karyawanUnit->pluck('nip'))
            ->when(
                !is_null($params['month']) || !is_null($params['year']),
                function ($query) use ($params) {
                    $query->whereMonth('tanggal', $params['month'])
                        ->whereYear('tanggal', $params['year']);
                },
                function ($query) {
                    /**
                     * DEFAULT IS MONTH OR YEAR NULL OR Empty
                     */
                    $now = now();
                    $query->whereMonth('tanggal', $now->format('m'))
                        ->whereYear('tanggal', $now->format('Y'));
                }
            )
            ->orderBy('tanggal')
            ->get();

        if ($jadwals->isEmpty()) {
            throw new Exception('Jadwal Kosong');
        }

        $resp = [
            "columns" => [],
            "rows" => [],
            "unit" => $params['user']->mKaryawan->unit,
        ];

        /**
         * SETUP ROWS
         */
        $jadwalUnit = collect([]);
        foreach ($karyawanUnit as $karyawan) {
            $jadwal = $jadwals->filter(function ($item) use ($karyawan) { // JIKA NIP SAMA
                return $item->nip === $karyawan->nip;
            })
                ->groupBy(function ($item) { // GROUP BY TANGGAL
                    return Carbon::make($item['tanggal'])->isoFormat('D dddd');
                })
                ->map(function ($shift) { // PER SHIFT
                    return $shift->map(function ($item) { // FILTER KODE SHIFT
                        return $item->libur == 1 ? 'Libur' : $item->kode_shift;
                    })->implode(', ');
                })
                ->all();

            $jadwalUnit->push([
                "{$karyawan->nip}-{$karyawan->nama}" => $jadwal,
                "nip" => $karyawan->nip
            ]);
        }

        $columns = collect([]);
        /**
         * Ambil jadwal staf yang pertama
         */
        $columnsData = collect(
            array_values($jadwalUnit->first())[0]
        );
        /**
         * jika kosong, maka ambil yang ke 2
         */
        if ($columnsData->keys()->isEmpty()) {
            $columnsData = collect(
                array_values($jadwalUnit->get(1))[0]
            );
        }
        $columns->push("nama");
        $columns->push($columnsData->keys()->all());
        $resp["columns"] = $columns->flatten()->all();
        $resp["rows"] = $jadwalUnit->map(function ($item) {
            // $newItem = collect(array_values($item)[0])->values();
            $newItem = collect(array_values($item)[0]);
            $newItem->prepend(array_keys($item)[0], "nama");
            $newItem->put("nip", array_values($item)[1]);

            return $newItem;
        });

        return $resp;
    }

    public function getTanggalMasukRecursive(Carbon $tgl, $nip)
    {
        $jadwal = Jadwal::where('nip', $nip)
            ->whereDate('tanggal', $tgl->format('Y-m-d'))
            ->first([
                'tanggal',
                'libur',
            ]);

        if ($jadwal->libur) {
            return $this->getTanggalMasukRecursive($tgl->addDays(1), $nip);
        }

        return $jadwal->tanggal;
    }
}

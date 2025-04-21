<table>
    <tr>
        <td></td>
        <td colspan="5">REKAP Keterlambatan</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">PT Catur Karsa Inkrisuba</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">Jl Panglima Sudirman No 02</td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="5">Keterlambatan karyawan pada tanggal {{ \Carbon\Carbon::make($start)->format('d-m-Y') }} -
            {{ \Carbon\Carbon::make($end)->format('d-m-Y') }}</td>
    </tr>
    <tr style="color: red">
        <td style="color: red" colspan="5">Jumlah Terlambat dari setiap karyawan ada di paling bawah</td>
    </tr>
</table>
{{-- PALING BANYAK TERLAMBAT --}}
<table>
    <thead>
        <tr>
            <td>Karyawan Terlambat</td>
            <td colspan="2">Unit Paling sering</td>
            <td>Durasi Paling Panjang</td>
            <td>Rata-rata Terlambat</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $telats->groupBy('nama')->count() }} Karyawan</td>
            @php
                $unitTerlambat = $telats->groupBy('jadwal.mUnit.nama')->map->sum('ttltelat');
                $unitTertinggi = $unitTerlambat->sort(function ($a, $b) {
                    if ($a == $b) {
                        return 0;
                    }
                    return $a > $b ? -1 : 1;
                });
                $unitMaxMenit = $unitTertinggi->first();
                $unitName = $unitTertinggi->keys()->first();
                $jam = floor($unitMaxMenit / 60);
                $menit = $unitMaxMenit % 60;
            @endphp
            <td colspan="2">
                {{ "Unit dengan keterlambatan tertinggi adalah: {$unitName} dengan keterlambatan {$jam} jam {$menit} menit" }}
            </td>
            @php
                $max = $telats->max('ttltelat');
                $jamMax = floor($max / 60);
                $menitMax = $max % 60;
            @endphp
            <td>{{ "{$jamMax} jam {$menitMax} menit" }}</td>
            @php
                $ttl = $telats->sum('ttltelat') / $telats->count();
                $jam = floor($ttl / 60);
                $menit = $ttl % 60;
            @endphp
            <td>{{ "{$jam} jam {$menit} menit" }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Total</th>
            <th>Terbilang</th>
        </tr>
    </thead>
    <tbody>
        @php
            $karyawanGrouping = collect(
                $telats->groupBy('nama')->map(function ($item) {
                    $sum = $item->sum('ttltelat');

                    $jam = floor($sum / 60);
                    $menit = $sum % 60;
                    return [
                        'ttl' => $item->count(),
                        'ttl_menit' => $sum,
                        'unit' => $item[0]->jadwal->mUnit->nama ?? '-',
                        'desc' => "{$jam} jam {$menit} menit",
                    ];
                }),
            )->sortBy('ttl_menit');
        @endphp
        @foreach ($karyawanGrouping as $keys => $karyawan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $keys }}</td>
                <td>{{ $karyawan['unit'] }}</td>
                <td>{{ $karyawan['ttl_menit'] }} Menit</td>
                <td>{{ $karyawan['ttl'] . 'x total keterlambatan ' . $karyawan['desc'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

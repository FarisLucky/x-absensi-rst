<table>
    <tr>
        <td></td>
        <td colspan="5">REKAPITULASI PRESENSI</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">PT Catur Karsa Inkrisuba</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">Jl Panglima Sudirman No 02</td>
    </tr>
</table>
<table>
    <tr>
        <td colspan="5">
            <h5>Rekapitulasi Kehadiran presensi pada bulan
                {{ $start }} / {{ $end }}
            </h5>
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Unit</th>
            <th>Total Jam Kerja</th>
            <th>Jadwal</th>
            <th>Libur</th>
            <th>Hari Kerja</th>
            <th>Hadir</th>
            <th>Telat</th>
            <th>Presentase %</th>
            <th>Alpa</th>
            <th>Cuti</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $unit)
            @foreach ($unit as $key => $item)
                @php
                    $jAktif = intval($item['jadwal']->ttl) - intval($item['libur']->ttl);
                    $jam = floor($item['ttl_kerja'] / 60);
                    $sisaMenit = $item['ttl_kerja'] % 60;
                @endphp
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $item['unit'] }}</td>
                    <td>{{ $jam . ' jam ' . $sisaMenit . ' menit' }}</td>
                    <td>{{ $item['jadwal']->ttl }}</td>
                    <td>{{ $item['libur']->ttl }}</td>
                    <td>{{ $jAktif }}</td>
                    <td>{{ $item['hadir']->ttl }}</td>
                    <td style="background-color: #fda4af">{{ $item['telat']->ttl }}</td>
                    <td style="background-color: #cbd5e1">
                        {{ $item['hadir']->ttl > 0 ? round((intval($item['hadir']->ttl) / $jAktif) * 100) : 0 }}
                    </td>
                    <td>{{ $item['alpa']->ttl }}</td>
                    <td>{{ $item['cuti']->ttl }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

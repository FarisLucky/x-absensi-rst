<table>
    <tr>
        <td></td>
        <td colspan="5">REKAPITULASI KARYAWAN</td>
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
        <td colspan="5">
            Presensi karyawan pada bulan {{ $month }} - {{ $year }}
            @if (!is_null($unit))
                - {{ $unit }}
            @endif
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Nip</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Jadwal</th>
            <th>Tepat</th>
            <th>Telat</th>
            <th>Alpa</th>
            <th>Cuti</th>
            <th>Izin</th>
            <th>Shift</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($karyawans as $karyawan)
            <tr>
                <td>{{ $karyawan->nip }}</td>
                <td>{{ $karyawan->nama }}</td>
                <td>{{ $karyawan->mUnit->nama }}</td>
                <td>{{ $karyawan->jadwal }}</td>
                <td>{{ $karyawan->tepat }}</td>
                <td>{{ $karyawan->telat }}</td>
                <td>{{ $karyawan->alpa }}</td>
                <td>{{ $karyawan->cuti }}</td>
                <td>{{ $karyawan->izin }}</td>
                <td>{{ $karyawan->tukar_jadwal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

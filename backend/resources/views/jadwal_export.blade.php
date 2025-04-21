<table>
    <tr>
        <td></td>
        <td colspan="5">Jadwal PRESENSI</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">PT Catur Karsa Inkrisuba</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">Dusun Krajan 2, Randumerak, Kec. Paiton, Kabupaten Probolinggo, Jawa Timur 67291</td>
    </tr>
    <tr>
        <td style="color: red">*Judul Jadwal jangan diubah</td>
    </tr>
    <tr>
        <td colspan="2">Unit: {{ $pembuat->mUnit->nama }}</td>
    </tr>
    <tr>
        <td colspan="2">Bulan: {{ $filter->format('F Y') }}</td>
        <td colspan="{{ $filter->endOfMonth() }}">
            <h5 style="text-align: center">TANGGAL</h5>
        </td>
    </tr>
    <tr>
        <td>Nip</td>
        <td>Nama</td>
        @foreach ($periods as $date)
            <td>{{ $date->format('d') }}</td>
        @endforeach
    </tr>
    @foreach ($karyawans as $karyawan)
        <tr>
            <td>{{ $karyawan->nip }}</td>
            <td>{{ $karyawan->nama }}</td>
        </tr>
    @endforeach
</table>
<table>
    <tr>
        <td>
            <h5>break </h5>
        </td>
        <td>
            <h5>JADWAL SHIFT </h5>
        </td>
    </tr>
    <tr>
        <td>Kode</td>
        <td>Nama (Masuk-Pulang)</td>
        <td>Absen</td>
        <td>Terlambat</td>
    </tr>
    @foreach ($shifts as $shift)
        <tr>
            <td>{{ $shift->kode }}</td>
            <td>{{ $shift->nama . '(' . $shift->jam_masuk . '-' . $shift->jam_pulang . ')' }}</td>
            <td>{{ $shift->mulai_absen }} menit</td>
            <td>{{ $shift->telat_masuk }} menit</td>
        </tr>
    @endforeach
    <tr>
        <td>L</td>
        <td>Libur</td>
    </tr>
</table>

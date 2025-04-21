<table>
    <tr>
        <td></td>
        <td colspan="5">REKAP PRESENSI</td>
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
        <td colspan="5">Presensi karyawan pada tanggal {{ $start . '-' . $end }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Nip</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Absen Masuk</th>
            <th>Absen Pulang</th>
            <th>Status</th>
            <th>Ket</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($presensies as $presensi)
            <tr>
                <td>{{ $presensi->nip }}</td>
                <td>{{ $presensi->nama }}</td>
                <td>{{ $presensi->nama_unit }}</td>
                <td>{{ \Carbon\Carbon::make($presensi->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $presensi->shift }}</td>
                <td>{{ $presensi->jam_masuk }}</td>
                <td>{{ $presensi->jam_pulang }}</td>

                @if ($presensi->jadwal_status == 3)
                    <td>-</td>
                @else
                    <td>{{ $presensi->masuk }}</td>
                @endif

                @if ($presensi->jadwal_status == 3)
                    <td>-</td>
                @else
                    <td>{{ $presensi->pulang }}</td>
                @endif

                @if ($presensi->jadwal_status == \App\Models\Jadwal::TIDAK_HADIR)
                    <td>ALPA</td>
                @elseif(
                    $presensi->libur == 1 &&
                        $presensi->jadwal_status == \App\Models\Jadwal::IZIN &&
                        $presensi->izin_detail_tanggal == $presensi->tanggal)
                    <td style="background-color: cornflowerblue">{{ $presensi->izin }}</td>
                @elseif($presensi->libur == 1 && $presensi->jenis_tukar)
                    <td style="background-color: darkviolet">TUKAR OFF</td>
                @elseif($presensi->libur == 1 && is_null($presensi->kode_shift))
                    <td style="background-color: blanchedalmond">LIBUR</td>
                @elseif(is_null($presensi->jadwal_status))
                    <td style="background-color: red">BELUM ABSEN</td>
                @elseif($presensi->jadwal_status == 7)
                    <td style="background-color: chocolate">SPPD</td>
                @else
                    <td>{{ $presensi->presensi_status }}</td>
                @endif

                <td>{{ $presensi->ket_terlambat }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

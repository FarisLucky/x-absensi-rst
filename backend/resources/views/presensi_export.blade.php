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
        <td colspan="5">7FFJ+8PR, Dusun Krajan 2, Randumerak, Kec. Paiton, Kabupaten Probolinggo, Jawa Timur 67291
        </td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="5">Presensi karyawan pada tanggal {{ $tgl->format('d-m-Y') }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Nip</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Absen Masuk</th>
            <th>Absen Pulang</th>
            <th>Lokasi Masuk</th>
            <th>Lokasi Pulang</th>
            <th>Total Jam</th>
            <th>Perangkat</th>
            <th>Status</th>
            <th>Ket (Jika Terlambat)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($presensies as $presensi)
            <tr>
                <td>{{ \Carbon\Carbon::make($presensi->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $presensi->shift }}</td>
                <td>{{ $presensi->nip }}</td>
                <td>{{ $presensi->nama }}</td>
                <td>{{ $presensi->nama_unit }}</td>
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
                <td>{{ $presensi->lok_masuk }}</td>
                <td>{{ $presensi->lok_pulang }}</td>
                <td>{{ $presensi->ttlkerja }}</td>
                <td>{{ $presensi->device }}</td>

                {{-- STATUS --}}
                @if ($presensi->status == \App\Models\Jadwal::TIDAK_HADIR)
                    <td>ALPA</td>
                @elseif(
                    $presensi->libur == 1 &&
                        $presensi->status == \App\Models\Jadwal::IZIN &&
                        $presensi->izin_detail_tanggal == $presensi->tanggal)
                    <td style="background-color: #06b6d4">{{ $presensi->izin }}</td>
                @elseif($presensi->libur == 1 && is_null($presensi->kode_shift))
                    <td style="background-color: #6b7280">LIBUR</td>
                @elseif(is_null($presensi->status))
                    <td style="background-color: #f43f5e">BELUM ABSEN</td>
                @else
                    <td>{{ $presensi->status_absen }}</td>
                @endif

                <td>{{ $presensi->ket_terlambat }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

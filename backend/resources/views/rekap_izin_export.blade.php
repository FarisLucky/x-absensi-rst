<table>
    <tr>
        <td></td>
        <td colspan="5">REKAP Perizinan</td>
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
        <td colspan="5">Perizinan karyawan pada tanggal {{ \Carbon\Carbon::make($start)->format('d-m-Y') }} -
            {{ \Carbon\Carbon::make($end)->format('d-m-Y') }}</td>
    </tr>
    <tr style="color: red">
        <td style="color: red" colspan="5">Jumlah Perizinan dari setiap karyawan ada di paling bawah</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Izin</th>
            <th>Total</th>
            <th>Rincian</th>
            <th>Alasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($izins as $izin)
            <tr>
                <td>{{ \Carbon\Carbon::make($izin->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $izin->nama }}</td>
                <td>{{ $izin->pemohon->mUnit->nama }}</td>
                <td>{{ $izin->izin }}</td>
                @if ($izin->jenis_table === App\Models\Izin::CUTI)
                    @php
                        $mulai = \Carbon\Carbon::make($izin->izinCuti->mulai)->format('d-m-Y');
                        $akhir = \Carbon\Carbon::make($izin->izinCuti->akhir)->format('d-m-Y');
                    @endphp
                    <td>{{ $izin->izinCuti->periode }} hari</td>
                    <td>{{ "{$mulai} - {$akhir}" }}</td>
                @else
                    <td>-</td>
                    <td>-</td>
                @endif
                <td>{{ $izin->ket }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

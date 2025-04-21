<table>
    <tr>
        <td></td>
        <td colspan="5">REKAP ABSENSI CKI</td>
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
        <td colspan="5">IZIN karyawan
            {{ $range_tanggal . '-' . $unit . '-' . $izin . '-' . $search }}
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Pengajuan</th>
            <th>Unit</th>
            <th>Nama</th>
            <th>Izin</th>
            <th>Mulai</th>
            <th>Akhir</th>
            <th>Masuk</th>
            <th>Periode</th>
            <th>Cuti Diambil</th>
            <th>Sisa</th>
            <th>Acc NIP</th>
            <th>Acc Nama</th>
            <th>Acc Pada</th>
            <th>Status</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($izins as $izin)
            <tr>
                <td>{{ $izin->created_at_cast }}</td>
                <td>{{ $izin->unit }}</td>
                <td>{{ $izin->nama }}</td>
                <td>{{ $izin->izin }}</td>
                <td>{{ $izin->mulai_cast }}</td>
                <td>{{ $izin->akhir_cast }}</td>
                <td>{{ $izin->masuk_cast }}</td>
                <td>{{ $izin->periode }}</td>
                <td>{{ $izin->cuti_diambil }}</td>
                <td>{{ $izin->sisa }}</td>
                <td>{{ $izin->acc_nip }}</td>
                <td>{{ $izin->acc_nama }}</td>
                <td>{{ $izin->acc_at_cast }}</td>
                <td>{{ $izin->status_desc }}</td>
                <td>{{ $izin->ket }}</td>
                <td>{{ $izin->ket }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

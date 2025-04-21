<table>
    <tr>
        <td></td>
        <td colspan="5">LIST KARYAWAN PT CKI</td>
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
</table>
<table>
    <tr>
        <td colspan="5">List Karyawan PT Catur Karsa Inkrisuba</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Foto</th>
            <th>Nip</th>
            <th>Nama</th>
            <th>Kelamin</th>
            <th>Unit</th>
            <th>Jabatan</th>
            <th>Tempat Lahir</th>
            <th>Tgl Lahir</th>
            <th>Pendidikan</th>
            <th>Agama</th>
            <th>Telp</th>
            <th>Tgl Masuk</th>
            <th>Tgl Resign</th>
            <th>BPJS TK</th>
            <th>BPJS Kesehatan</th>
            <th>Nomor SIP</th>
            <th>SIP Terbit</th>
            <th>SIP Akhir</th>
            <th>Alamat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($karyawans as $karyawan)
            <tr>
                <td>
                    {{-- @if (!is_null($karyawan->photo))
                        <img src="{{ $karyawan->photo_url_cast }}" width="60px">
                    @else --}}
                    <img src="{{ public_path('user.jpg') }}" width="40px">
                    {{-- @endif --}}
                </td>
                <td>{{ $karyawan->nip }}</td>
                <td>{{ $karyawan->nama }}</td>
                <td>{{ $karyawan->sex }}</td>
                <td>{{ optional($karyawan->mUnit)->nama }}</td>
                <td>
                    @foreach ($karyawan->jabatans as $jabatan)
                        <span>{{ optional($jabatan->mJabatan)->nama }}, </span>
                    @endforeach
                </td>
                <td>{{ $karyawan->tempat_lahir }}</td>
                <td>{{ $karyawan->tgl_lahir_cast }}</td>
                <td>{{ $karyawan->pendidikan }}</td>
                <td>{{ $karyawan->agama }}</td>
                <td>{{ $karyawan->telp }}</td>
                <td>{{ $karyawan->tgl_masuk }}</td>
                <td>{{ $karyawan->tgl_resign }}</td>
                <td>{{ $karyawan->bpjs_tk }}</td>
                <td>{{ $karyawan->bpjs_ks }}</td>
                @if ($karyawan->mKaryawanDetail->isNotEmpty())
                    @foreach ($karyawan->mKaryawanDetail as $detail)
                        @if (strtolower($detail->jenis) === 'sip')
                            <td>{{ $detail->nomor }}</td>
                            <td>{{ $detail->terbit_cast }}</td>
                            <td>{{ $detail->akhir_cast }}</td>
                        @endif
                    @endforeach
                @else
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                @endif
                <td>{{ $karyawan->alamat }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

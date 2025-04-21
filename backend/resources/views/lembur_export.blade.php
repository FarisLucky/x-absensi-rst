<table>
    <tr>
        <td></td>
        <td colspan="5">REKAP Lembur</td>
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
            Lembur karyawan pada tanggal {{ \Carbon\Carbon::make($start)->format('d-m-Y') }} -
            {{ \Carbon\Carbon::make($end)->format('d-m-Y') }}
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Bukti</th>
            <th>Lembur</th>
            <th>Harga</th>
            <th>Nip</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Jenis Absen</th>
            <th>Keterangan</th>
            <th>Mulai</th>
            <th>Akhir</th>
            <th>Tanggal Akhir</th>
            <th>Absen Masuk</th>
            <th>Absen Pulang</th>
            <th>Acc 1</th>
            <th>Acc 1 At</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $ttl = 0;
        @endphp
        @foreach ($lemburs as $lembur)
            <tr>
                <td>{{ \Carbon\Carbon::make($lembur->tanggal)->format('d-m-Y') }}</td>
                <td>
                    {{-- {{ 'https://gsabsen.my.id/backend/public/storage' . $lembur->bukti }} --}}
                    @if (!is_null($lembur->bukti))
                        <img src="{{ storage_path('app/public/' . $lembur->bukti) }}" width="50" height="70">
                        {{-- <img src="{{ 'https://gsabsen.my.id/backend/public/storage' . $lembur->bukti }}"> --}}
                    @else
                        TIDAK ADA BUKTI
                    @endif
                </td>
                <td>{{ $lembur->lembur }}</td>
                <td>{{ number_format($lembur->harga, 2, ',', '.') }}</td>
                <td>{{ $lembur->nip }}</td>
                <td>{{ $lembur->nama }}</td>
                <td>{{ $lembur->mUnit->nama }}</td>
                <td>{{ $lembur->absen }}</td>
                <td>{{ $lembur->ket }}</td>
                <td>{{ $lembur->mulai }}</td>
                <td>{{ $lembur->akhir }}</td>
                <td>{{ $lembur->tgl_akhir }}</td>
                <td>{{ $lembur->masuk }}</td>
                <td>{{ $lembur->pulang }}</td>
                <td>{{ $lembur->acc1By->nama }}</td>
                <td>{{ !is_null($lembur->acc1_at) ? \Carbon\Carbon::make($lembur->acc1_at)->format('d-m-Y H:i') : 'BELUM DI ACC' }}
                </td>
                @if ($lembur->acc_status == 1)
                    <td>DITERIMA</td>
                @elseif($lembur->acc_status == 2)
                    <td style="background-color: #fb7185">DITOLAK ({{ $lembur->tolak }})</td>
                @elseif(!is_null($lembur->acc_status))
                    <td>-</td>
                @endif
            </tr>
            @if ($lembur->acc_status !== 2)
                @php
                    $ttl += (int) $lembur->harga;
                @endphp
            @endif
        @endforeach
        <tr>
            <td colspan="3">Total Pengeluaran Lembur</td>
            <td>{{ number_format($ttl, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

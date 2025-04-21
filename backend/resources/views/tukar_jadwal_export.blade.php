<table>
    <tr>
        <td></td>
        <td colspan="5">REKAP PT CKI</td>
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
        <td colspan="5">TUKAR SHIFT karyawan
            {{ $range_tanggal . '-' . $unit . '-' . $jenis . '-' . $search }}
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>Unit</th>
            <th>Pengajuan</th>
            <th>Tanggal Pihak 1</th>
            <th>Shift Asli 1</th>
            <th>Shift Pihak 1</th>
            <th>Pihak 1</th>
            <th>Tanggal Pihak 2</th>
            <th>Shift Asli 2</th>
            <th>Shift Pihak 2</th>
            <th>Pihak 2</th>
            <th>Acc Atasan</th>
            <th>Acc Atasan Tanggal</th>
            <th>Acc SDM</th>
            <th>Acc SDM Tanggal</th>
            <th>Keterangan</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Keterangan Tolak</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tukarJadwals as $tukarJadwal)
            <tr>
                <td>{{ optional($tukarJadwal->pihak1->mUnit)->nama }}</td>
                <td>{{ \Carbon\Carbon::make($tukarJadwal->tanggal)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::make($tukarJadwal->tgl_pihak1)->format('d-m-Y') }}</td>
                <td>{{ $tukarJadwal->shift_pihak1 }}</td>
                @if ($tukarJadwal->jenis == 2)
                    <td>Libur</td>
                @else
                    <td>{{ $tukarJadwal->kd_shift_pihak2 }}</td>
                @endif
                <td>{{ $tukarJadwal->nama_pihak1 }}</td>
                <td>{{ \Carbon\Carbon::make($tukarJadwal->tgl_pihak2)->format('d-m-Y') }}</td>
                <td>{{ $tukarJadwal->kd_shift_pihak2 }}</td>
                @if ($tukarJadwal->jenis == 2)
                    <td>{{ "{$tukarJadwal->kd_shift_pihak2}, {$tukarJadwal->kd_shift_pihak1}" }}</td>
                @else
                    <td>{{ $tukarJadwal->kd_shift_pihak2 }}</td>
                @endif
                <td>{{ $tukarJadwal->nama_pihak2 }}</td>
                <td>{{ optional($tukarJadwal->acc)->nama }}</td>
                <td>{{ !is_null($tukarJadwal->acc_by_at) ? \Carbon\Carbon::make($tukarJadwal->acc_by_at)->format('d-m-Y H:i') : '' }}
                </td>
                <td>{{ optional($tukarJadwal->sdm)->nama }}</td>
                <td>{{ !is_null($tukarJadwal->acc_at) ? \Carbon\Carbon::make($tukarJadwal->acc_at)->format('d-m-Y H:i') : '' }}
                </td>
                <td>{{ $tukarJadwal->ket }}</td>
                <td>{{ $tukarJadwal->jenis_desc }}</td>
                <td>{{ $tukarJadwal->acc_status_desc }}</td>
                <td>{{ optional($tukarJadwal->tolak)->ket }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

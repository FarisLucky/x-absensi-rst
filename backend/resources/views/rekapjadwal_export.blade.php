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
    <tr></tr>
    <tr>
        <td colspan="5">
            Jadwal Unit {{ $unitName }}
        </td>
    </tr>
</table>
@foreach ($results as $idx => $data)
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold">Jadwal Periode {{ \Carbon\Carbon::createFromFormat('Y-m',$idx)->format('M Y') }}</th>
            </tr>
            <tr>
                <th>Nama</th>
                @foreach ($data['label'] as $column)
                    <th style="text-align: center">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data['row'] as $karyawan => $jadwal)
                <tr>
                    <td>
                        {{ $karyawan }}
                    </td>
                    @if (!is_null($jadwal))
                        @foreach ($jadwal as $shift)
                            <td style="text-align: center">
                                @if (is_array($shift))
                                    @foreach ($shift as $s)
                                        @if ($s->libur && $s->kode_shift !== null)
                                            {{ $s->kode_shift }}
                                        @elseif($s->libur && $s->kode_shift === null)
                                            L
                                        @else
                                            {{ $s->kode_shift }}
                                        @endif
                                    @endforeach
                                @else
                                    @if ($shift->libur && $shift->kode_shift !== null)
                                        {{ $shift->kode_shift }}
                                    @elseif($shift->libur && $shift->kode_shift === null)
                                        L
                                    @else
                                        {{ $shift->kode_shift }}
                                    @endif
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

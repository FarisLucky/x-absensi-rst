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
            Presensi karyawan pada bulan {{ $month }} - {{ $year }}
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            @foreach ($presensies['columns'] as $key => $column)
                @if ($key < 1)
                    <th>{{ $column }}</th>
                @else
                    <th colspan=2>{{ $column }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($presensies['rows'] as $rows)
            <tr>
                @foreach (json_decode(json_encode($rows), true) as $key => $row)
                    @php
                        $countRow = count($row);
                    @endphp
                    @foreach ($row as $item)
                        @if (str_contains($item, ':'))
                            @php
                                $val = explode(':', $item);
                            @endphp
                            @if (trim($val[1]) === 'TEPAT')
                                <td colspan="{{ $countRow < 2 ? 2 : 1 }}" style="background-color: #4CAF50">
                                    {{ $item }}
                                </td>
                            @elseif(trim($val[1]) === 'TELAT')
                                <td colspan="{{ $countRow < 2 ? 2 : 1 }}" style="background-color: #FFC107">
                                    {{ $item }}</td>
                            @elseif(trim($val[1]) === 'ALPA')
                                <td colspan="{{ $countRow < 2 ? 2 : 1 }}" style="background-color: #F44336">
                                    {{ $item }}</td>
                            @elseif(trim($val[1]) === 'IZIN')
                                <td colspan="{{ $countRow < 2 ? 2 : 1 }}" style="background-color: #2196F3">
                                    {{ $item }}</td>
                            @endif
                        @else
                            @if ($key < 1)
                                <td>{{ $item }}</td>
                            @else
                                <td colspan="{{ $countRow < 2 ? 2 : 1 }}">{{ $item }}</td>
                            @endif
                        @endif
                        {{-- @if (count($row) < 2)
                            <td></td>
                        @endif --}}
                    @endforeach
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

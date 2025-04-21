<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>
    {{ $tanggal->first()->isoFormat('MMMM YYYY') }}
  </title>
  <style>
    * {
      font-size: 9px;
    }

    strong {
      font-size: 12px !important;
    }
  </style>
</head>

<body>
  <strong class="d-block text-center fs-2 mb-2">
    Jadwal, {{ $tanggal->first()->isoFormat('MMMM YYYY') }}
  </strong>
  @foreach ($jadwal as $item)
    @if ($item->jadwal->count() > 0)
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th colspan="{{ $tanggal->count() + 2 }}" class="fw-boldest">
              {{ $item->nama_jenis_shift }} (
              @foreach ($item->shift as $shift)
                {{ $shift->nama_shift }} = {{ $shift->kode_shift }}
                {{ Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                - {{ Carbon\Carbon::parse($shift->jam_keluar)->format('H:i') }}
                @if (!$loop->last)
                  {{ ', ' }}
                @endif
              @endforeach
              )
            </th>
          </tr>
          <tr>
            <th class="text-center">No </th>
            <th class="text-center w-20">Name</th>
            <th class="text-center w-20">Phone</th>
            @foreach ($tanggal as $tgl)
              <th class="text-center">{{ $tgl->format('d') }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @if ($item->jadwal->count() == 0)
            <tr>
              <td colspan="{{ $tanggal->count() + 2 }}" class="text-center">
                Tidak ada data
              </td>
            </tr>
          @endif
          @foreach ($item->jadwal as $jadwal)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $jadwal->nama_karyawan }}</td>
              <td>{{ $jadwal->no_hp }}</td>
              @foreach ($tanggal as $tgl)
                <td class="text-center">
                  @if ($jadwal->jadwal->contains('tanggal', $tgl->format('Y-m-d')))
                    @if ($jadwal->jadwal->where('tanggal', $tgl->format('Y-m-d'))->count() > 1)
                      @foreach ($jadwal->jadwal->where('tanggal', $tgl->format('Y-m-d')) as $item)
                        {{ $item->kode_shift }}
                        @if (!$loop->last)
                          {{ ' - ' }}
                        @endif
                      @endforeach
                    @else
                      {{ $jadwal->jadwal->where('tanggal', $tgl->format('Y-m-d'))->first()->kode_shift }}
                    @endif
                  @else
                    <span class="text-danger">OFF</span>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>

      @if (!$loop->last)
        <div class="mb-3"></div>
      @endif
    @endif
  @endforeach
</body>

</html>

<tr>
  <td colspan="{{ $tanggal->count() + 2 }}" class="text-center">
    Jadwal, {{ $tanggal->first()->isoFormat('MMMM YYYY') }}
  </td>
</tr>
@foreach ($jenis_shift as $item)
  <table>
    <thead>
      <tr>
        <td colspan="{{ $tanggal->count() + 2 }}" class="text-center">
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
        </td>
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

  </table>
  @if (!$loop->last)
    <tr></tr>
    <tr></tr>
  @endif
@endforeach

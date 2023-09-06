<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Laporan Absensi {{$user->karyawan->nama_karyawan}}</title>
</head>

<body>
  <h3 style="text-align: center">Laporan Absensi {{$user->karyawan->nama_karyawan}}</h3>
  <h4 style="text-align: center">Periode {{$mulai}} s/d {{$selesai}}</h4>
  <br>
  <table class="table table-light" width="100%">
    <thead class="thead-light">
      <tr>
        <td>NPK</td>
        <td>Waktu Absen</td>
        <td>Jenis Absen</td>
        <td>Status Absen</td>
        <td>Lokasi Absen</td>
        <td>Keterangan</td>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $item)
      <tr>
        <td>{{ $user->karyawan->npk }}</td>
        <td>{{ $item->waktu_absen }}</td>
        <td>
          @if ($item->type_absen == 1) Absen Masuk @endif
          @if ($item->type_absen == 2) Absen Istirahat @endif
          @if ($item->type_absen == 3) Selesai Istirahat @endif
          @if ($item->type_absen == 4) Absen Pulang @endif
        </td>
        <td>
          @if ($item->status_absen == 1)
          @if ($item->type_absen == 4)
          <p style="margin-bottom: 0; color: green">SUDAH ABSEN PULANG</p>
          @else
          <p style="margin-bottom: 0; color: green">TEPAT WAKTU</p>
          @endif
          @else
          @if ($item->type_absen == 4)
          <p style="margin-bottom: 0; color: red">BELUM ABSEN PULANG</p>
          @else
          <p style="margin-bottom: 0; color: red">TELAT</p>
          @endif
          @endif
        </td>
        <td>{{ $item->nama_lokasi }}</td>
        <td>{{ $item->keterangan }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
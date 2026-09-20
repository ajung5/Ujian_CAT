<?php
  $bulanpendek["01"] = "Jan";
  $bulanpendek["02"] = "Feb";
  $bulanpendek["03"] = "Mar";
  $bulanpendek["04"] = "Apr";
  $bulanpendek["05"] = "Mei";
  $bulanpendek["06"] = "Jun";
  $bulanpendek["07"] = "Jul";
  $bulanpendek["08"] = "Ags";
  $bulanpendek["09"] = "Sep";
  $bulanpendek["10"] = "Okt";
  $bulanpendek["11"] = "Nov";
  $bulanpendek["12"] = "Des";
?>
<table class="table table-bordered table-striped table-hover table-condensed">
  <thead>
    <tr>
      <th width="55px">#</th>
      <th>Paket</th>
      <th>Deskripsi</th>
      <th>KKM</th>
      <th>Waktu</th>
      <th>Tgl Dibuat</th>
      <th width="50px">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = $jawabs->firstItem(); ?>
    @if($jawabs->count())
    @foreach($jawabs as $jawab)
    <?php
      $tanggal = explode(" ", $jawab->created_at);
      $tanggal = explode("-", $tanggal[0]);
      $tanggal = $tanggal[2].' '.$bulanpendek[$tanggal[1]].' '.$tanggal[0];
    ?>
    <tr>
      <td>{{ $no++ }}</td>
      <td>{{ $jawab->paket }}</td>
      <td>{{ $jawab->deskripsi }}</td>
      <td>{{ $jawab->kkm }}</td>
      <td>{{ $jawab->waktu/60 }} menit</td>
      <td>{{ $tanggal }}</td>
      <td>
        <a href="{{ url('/detail-hasil/'.$jawab->id_soal) }}" class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Detail</a>
      </td>
    </tr>
    @endforeach
    @else
    <tr><td colspan="7" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
    @endif
  </tbody>
</table>
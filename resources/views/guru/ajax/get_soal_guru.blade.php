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
<table class="table table-bordered table-striped table-hover table-condensed" id="tabelsoal">
  <thead>
    <tr>
      <th style="width: 50px">#</th>
      <th style="text-align: center;">ID <small>Soal</small></th>
      <th>Paket <small>Soal</small></th>
      <th>Deskripsi</th>
      <th>KKM</th>
      <th>Waktu</th>
      <th>Tgl Dibuat</th>
      <th style="width: 130px; text-align: center;">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = $soals->firstItem(); ?>
    @if($soals->count())
    @foreach($soals as $soal)
    <?php
      $tanggal = explode(" ", $soal->created_at);
      $tanggal = explode("-", $tanggal[0]);
      $tanggal = $tanggal[2].' '.$bulanpendek[$tanggal[1]].' '.$tanggal[0];
    ?>
    <tr>
      <td>{{ $no++ }}</td>
      <td style="text-align: center;">{{ $soal->id }}</td>
      <td>{{ $soal->paket }}</td>
      <td>{{ $soal->deskripsi }}</td>
      <td>{{ $soal->kkm }}</td>
      <td>{{ $soal->waktu/60 }} menit</td>
      <td>{{ $tanggal }}</td>
      <td style="text-align: center;">
        <a href="{{ url('/edit-soal/'.$soal->id) }}" class="btn btn-xs btn-success" data-toggle='tooltip' title="Ubah Soal"><i class="fa fa-pencil-square-o"></i></a>
        <a href="{{ url('/detail-soal/'.$soal->id) }}" class="btn btn-xs btn-primary" data-toggle='tooltip' title="Detail Soal"><i class="fa fa-search"></i></a>
        <a href="{{ url('/hapus-soal/'.$soal->id) }}" class="btn btn-xs btn-danger" target="_blank" data-toggle='tooltip' title="Hapus Soal"><i class="fa fa-trash"></i></a>
      </td>
    </tr>
    @endforeach
    @else
    <tr><td colspan="7" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
    @endif
  </tbody>
</table>
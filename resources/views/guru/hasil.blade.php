@extends('layouts/guru_baru')
@section('title', 'Laporan')
@section('content')
<?php
  include(app_path().'/functions/koneksi.php');
  $sapaan = Auth::user()->jk;
  if ($sapaan == "L") {
    $sapaan = "Pak";
  }else{
    $sapaan = "Ibu";
  }
?>
<div class="col-md-12 dash-left">
  <ol class="breadcrumb">
    <li><a href="{{ url('/guru') }}">Home</a></li>
    <li class="active">Laporan</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Laporan</div>
    <div class="panel-body">
      <div class="alert alert-info" role="alert"><b><i class="fa fa-info-circle"></i> Tips: </b>Dibawah ini daftar paket soal yang telah dikerjakan oleh siswa. Klik Tombol Detail untuk melakukan proses rekap serta melihat statistik jawaban siswa.</div>
      <hr class="clearfix">
      <div class="form-horizontal" style="margin-bottom: 15px">
        <input type="text" class="form-control" id="q" placeholder="Cari berdasarkan Paket soal (Ketik lalu enter)">
      </div>
      <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading_cari" style="display: none;">
      <div id="wrap-hasil" class="table-responsive">
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
        {!! $jawabs->render() !!}
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(document).ready(function() {
    $("#q").keyup(function(e){
      if(e.keyCode == 13)
      {
        $("#loading_cari").show();
        var q = $("#q").val();
        $.ajax({
          type: "POST",
          url: "{{ url('/get-hasil-guru') }}",
          data: 'q='+q,
          success: function(data){
            $("#loading_cari").hide();
            $("#wrap-hasil").hide().html(data).fadeIn(350);
          }
        });
      }
    });
  });
</script>
@endsection
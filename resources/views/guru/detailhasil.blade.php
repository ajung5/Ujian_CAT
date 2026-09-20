@extends('layouts/guru_baru')
@section('title', 'Hasil ujian per kelas')
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
<div class="col-sm-12 col-md-8 col-lg-8 dash-left">
  <ol class="breadcrumb">
    <li><a href="{{ url('/guru') }}">Home</a></li>
    <li><a href="{{url('hasil-guru')}}">Laporan</a></li>
    <li class="active">{{$soal->paket}}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Laporan <b>{{$soal->paket}}</b> </div>
    <div class="panel-body">
      <table class="table table-bordered" id="tabelsoal">
        <thead>
          <tr>
            <th>Kelas</th>
            <th style="text-align: center;" width="300px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @if($jawabs->count())
        @foreach($jawabs as $jawab)
        <tr>
          <td>{{ $jawab->nama_kelas }}</td>
          <td align="center">
            <a href="{{ url('downloadlaporanperkelas/'.$jawab->id_kelas.'/'.$jawab->id_soal) }}" class="btn btn-xs btn-success" data-toggle="tooltip" title="Download data hasil Ujian Ke dalam format Excel"><i class="fa fa-file-excel-o"></i> Rekap Nilai</a>

            <a href="{{ url('detail-hasil-soal/'.$jawab->id_kelas.'/'.$jawab->id_soal) }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" title="Detail rekap ujian per kelas."><i class="fa fa-search"></i> Detail</a>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Akan menghapus seluruh hasil ujian dari kelas yang dipilih. Data yang dihapus tidak bisa dikembalikan." id="del{{ $jawab->id_kelas }}"><i class="fa fa-trash-o"></i> Hapus</button>
            <a href="{{ url('tampilhasil/'.$jawab->id_kelas.'/'.$jawab->id_soal) }}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Download seluruh data hasil Ujian Ke dalam format Excel" target="_blank"><i class="fa fa-desktop"></i> Tampil</a></td>
          </td>
        </tr>
        <tr style="display: none;">
          <td colspan="2" class="alert alert-danger">Data diatas berhasil dihapus. <i>Refresh</i> halaman untuk melihat perubahannya.</td>
        </tr>
        <script type="text/javascript">
          
        </script>
        @endforeach
        @endif
        </tbody>
      </table>
      <p>{!! str_replace('/?', '?', $jawabs->render()) !!}</p>
    </div>
  </div>
</div>
<div class="col-sm-12 col-md-4 col-lg-4 dash-right">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">Aktifitas Terkini</h4>
    </div>
    <div class="panel-body">
      <ul class="media-list user-list">
      @if($aktifitas->count())
      @foreach($aktifitas as $data)
      <?php
        $tanggal_aktifitas = explode(" ", $data->created_at);
        $tanggal_aktifitas = explode("-", $tanggal_aktifitas[0]);
        $tanggal_aktifitas = $tanggal_aktifitas[2].' '.$bulanpendek[$tanggal_aktifitas[1]].' '.$tanggal_aktifitas[0];
        if ($data->gambar != "") {
          $gambar_aktifitas = $data->gambar;
        }else{
          $gambar_aktifitas = 'noimage.jpg';
        }
      ?>
        <li class="media">
          <div class="media-left">
            <a href="#">
              <img class="media-object img-thumbnail" src="{{ url('img/'.$gambar_aktifitas) }}" alt="">
            </a>
          </div>
          <div class="media-body">
            <h4 class="media-heading nomargin"><a href="#">{{ $data->nama_user }}</a></h4>
            {{ $data->nama }}
            <small class="date"><i class="fa fa-clock-o"></i> {{ $tanggal_aktifitas }}</small>
          </div>
        </li>
      @endforeach
      @endif
      </ul>
      <a href="{{ url('/aktifitas') }}" class="btn btn-success" style="display: block; width: 100%; margin: 10px 0 0 0">Selengkapnya</a>
    </div>
  </div>
</div>
@endsection
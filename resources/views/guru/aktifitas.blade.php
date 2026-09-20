@extends('layouts/guru_baru')
@section('title', 'Aktifitas')
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
<div class="col-sm-12 dash-left">
  <ol class="breadcrumb">
    <li><a href="{{ url('/guru') }}">Home</a></li>
    <li class="active">Aktifitas</li>
  </ol>
</div>
<div class="col-sm-12 dash-right">
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
      {!! $aktifitas->render() !!}
    </div>
  </div>
</div>
@endsection
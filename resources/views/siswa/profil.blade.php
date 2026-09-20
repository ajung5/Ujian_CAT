@extends('layouts/siswa_baru')
@section('title', 'Profil')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li class="active">Profil</li>
@endsection
@section('content')
<?php
  if ($user->jk == 'L') {
    $jk = 'Laki-laki';
  }else{
    $jk = 'Perempuan';
  }
?>
<div class="col-md-12">
  <div class="card">
    <div class="card-header bg-white">
      <div class="media">
        <div class="media-body">
          <h4 class="card-title">Profil</h4>
        </div>
      </div>
    </div>
    <div style="padding: 15px">
      <?php
        if (Auth::user()->gambar != "") {
          $foto = Auth::user()->gambar;
        }else{
          $foto = 'siswa.png';
        }
      ?>
      <div class="row">
        <div class="col-sm-3 col-md-3">
          <img src="{{ url('/img/'.$foto) }}" alt="" class="img-rounded img-thumbnail" />
        </div>
        <div class="col-sm-9 col-md-9">
          <blockquote>
            <h3>{{ $user->nama }}</h3> <small><cite title="Source Title">{{ $user->no_induk }}</cite></small>
          </blockquote>
          <p>
            <i class="sidebar-menu-icon fa fa-envelope" aria-hidden="true"></i> {{ $user->email }}<br/>
            <i class="sidebar-menu-icon fa fa-venus-mars" aria-hidden="true"></i> {{ $jk }}<br />
            <i class="sidebar-menu-icon fa fa-drivers-license" aria-hidden="true"></i> {{ $user->nama_kelas }}
          </p>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
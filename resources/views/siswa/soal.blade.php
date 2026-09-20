@extends('layouts/siswa_baru')
@section('title', 'Soal Ujian')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li class="active">Soal Ujian</li>
@endsection
@section('content')
<?php include(app_path().'/functions/koneksi.php'); ?>
<div class="card-columns">
  @if($distribusisoal->count())
  @foreach($distribusisoal as $data_soal)
  <?php
    $id_user = Auth::user()->id;
    $cek_jawab = $conn->query("SELECT * FROM jawabs WHERE id_soal='$data_soal->id_soal' AND id_user='$id_user' AND status='Y'")->num_rows;
    if ($cek_jawab == 0) {
  ?>
  <div class="card">
    <div class="card-header bg-white center">
      <h4 class="card-title"><a href="{{ url('/soal-siswa/'.$data_soal->id_soal) }}">{{ $data_soal->paket }}</a></h4>
    </div>
    <div class="card-block">
      <p class="m-b-0" style="color: #a6aab2">
        {{ $data_soal->deskripsi }}
      </p>
    </div>
  </div>
  <?php } ?>
  @endforeach
  @else
  <div class="alert alert-info">
    <i class="fa fa-info-circle" aria-hidden="true"></i> Belum ada paket soal untuk dikerjakan.
  </div>
  @endif
</div>
@endsection
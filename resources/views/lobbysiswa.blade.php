@extends('layouts/lobby')
@section('content')
<?php
    include(app_path() . '/functions/koneksi.php');

    $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM schools";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $namasekolah = $row["nama"];
            $logosekolah = $row['logo'];
        }
    } else {
        $namasekolah = "";
    }
    $conn->close();
?>
<center>
  <div style="color:#188eb0; font-size:12pt; margin: 15px 0 0 0;">Aplikasi Ujian Berbasis Komputer</div>
  <h1 style="color:#17bce6; margin: 15px 0 0 0; font-weight: bold; font-size:3em">{{ $namasekolah }}</h1>
</center>
<div class="row" style="margin: 35px 0 0 0;">
  <div class="col-md-5 col-md-offset-1">
    <div class="wrapiconcommon">
      <center>
        <img src="{!! url('img/iconujian.png') !!}" alt="" class="img img-responsive">
        <p style="margin:20px 0 0 0;"> <a href="{{ route('siswa.index') }}">
          <button type="button" class="btn btn-success">Ujian</button>
          </a> </p>
        <p style="margin:20px 0 0 0;">Masuk untuk mengerjakan soal ujian.</p>
      </center>
    </div>
  </div>
  <div class="col-md-5">
    <div class="wrapiconcommon">
      <center>
        <img src="{!! url('img/iconlatihan.png') !!}" alt="" class="img img-responsive">
        <p style="margin:20px 0 0 0;"> <a href="{{ url('/latihan') }}">
          <button type="button" class="btn btn-warning">Latihan</button>
          </a> </p>
        <p style="margin:20px 0 0 0;">Masuk untuk belajar dan berlatih soal.</p>
      </center>
    </div>
  </div>
  <div class="clearfix"></div>
  <center><button type="button" class="btn btn-primary btn-lg" onclick="self.history.back()"><span class="glyphicon glyphicon-share-alt"></span> Kembali</button></center>
</div>
<div class="clearfix"></div>
<div style="background:#323232; color:#fff; margin: 15px 0 0 0; padding: 20px;">
  <marquee onmouseover="this.stop()" onmouseout="this.start()" SCROLLAMOUNT="5">
  Selamat datang di Aplikasi Ujian Berbasis Komputer - {{ $namasekolah }}
  </marquee>
</div>
@endsection 
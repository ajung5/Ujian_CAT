<?php use Carbon\Carbon; ?>

<?php
  /*$tm = Carbon::today();
  $ts = new Carbon('2017-04-21');
  $interval = $tm->diff($ts);
  $interval =  $interval->format('%a');
  if($interval > 0){*/
?>
@extends('layouts/welcome')
@section('content')
<link rel="stylesheet" href="{{ url('/assets/assets/libs/sweetalert/dist/sweetalert.css') }}">
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{ url('/assets/assets/libs/sweetalert/dist/sweetalert.min.js') }}"></script>
<script>
  // $( window ).on( "load", function() { swal("Terimakasih telah menggunakan aplikasi Ujian Tipamedia. Anda menggunakan aplikasi versi trial. Masa aktif aplikasi Anda adalah  hari lagi."); });
  $(document).ready(function() {

    
  });
</script>
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
  //$conn->close();
?>
<center>
  <div style="color:#188eb0; font-size:12pt; margin: 15px 0 0 0;">Aplikasi Ujian Berbasis Komputer</div>
  <h1 style="color:#17bce6; margin: 15px 0 0 0; font-weight: bold; font-size:3em">{{ $namasekolah }}</h1>
</center>
<div class="row" style="margin: 35px 0 0 0;">
  <div class="col-md-5 col-md-offset-1">
    <div class="wrapiconcommon">
      <center>
        <img src="{!! url('img/guru.png') !!}" alt="" class="img img-responsive">
        <p style="margin:20px 0 0 0;"> <a href="{{ url('guru') }}">
          <button type="button" class="btn btn-success">Portal Guru</button>
          </a> </p>
        <p style="margin:20px 0 0 0;">Akses Akun Guru atau Admin Anda.</p>
      </center>
    </div>
  </div>
  <div class="col-md-5">
    <div class="wrapiconcommon">
      <center>
        <img src="{!! url('img/siswa.png') !!}" alt="" class="img img-responsive">
        <p style="margin:20px 0 0 0;"> <a href="{{ url('lobby-siswa') }}">
          <button type="button" class="btn btn-warning">Portal Siswa</button>
          </a> </p>
        <p style="margin:20px 0 0 0;">Masuk ke sistem sebagai Siswa.</p>
      </center>
    </div>
  </div>
</div>
<div class="clearfix"></div>
<div style="background:#323232; color:#fff; margin: 15px 0 0 0; padding: 20px;">
  <marquee onmouseover="this.stop()" onmouseout="this.start()" SCROLLAMOUNT="5">
  Selamat datang di Aplikasi Ujian Berbasis Komputer - {{ $namasekolah }}
  </marquee>
</div>
@endsection

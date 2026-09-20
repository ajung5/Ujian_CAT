@extends('layouts/siswa_baru')
@section('title', 'Selamat datang')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li class="active">Anda berada di Home</li>
@endsection
@section('content')
<div class="col-md-12">
  <div class="card">
    <div class="card-header bg-white">
      <div class="media">
        <div class="media-body">
          <h4 class="card-title">Selamat Datang</h4>
          <p class="card-subtitle"></p>
        </div>
      </div>
    </div>
    <div style="padding: 15px">
      Hai <b>{{ Auth::user()->nama }}</b>. Aplikasi ujian ini dirancang untuk memudahkan proses ujian. Ikutilah instruksi Guru untuk mengoperasikan aplikasi ini dengan benar.
    </div>
  </div>
</div>
@endsection
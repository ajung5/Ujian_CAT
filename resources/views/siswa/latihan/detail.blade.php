@extends('layouts/siswa_baru')
@section('title', 'Latihan')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li><a href="{{ url('/latihan') }}">Latihan</a></li>
  <li class="active">Detail: {{ $materi->judul }}</li>
@endsection
@section('content')
<style type="text/css" media="screen">
  .hideGambar{
    overflow:hidden; height:100px
  }
  .showGambar{
    text-align: center;
    background: #d6dbd7;
    padding: 15px 0;
    cursor: pointer;
  }
  .showGambar:hover{
    background: #c4c4c4;
  }
</style>
<h1 class="page-heading h2">{{ $materi->judul }}</h1>
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <?php if ($materi->gambar != "") { ?>
        <div class="hideGambar" id="wrap-gambar">
          <img src="{{ url('/img/materi/'.$materi->gambar) }}" alt="img" style="width:100%;">
        </div>
        <div class="showGambar">Tampil Gambar</div>
      <?php } ?>
      <div class="card-block">
        {!! $materi->isi !!}
        <hr>
        <?php if ($soals != "EM") { ?>
          <div class="row">
            <div class="col-md-12">
            <h4>Soal-soal latihan</h4>
            <hr>
            @if($soals->count())
            @foreach($soals as $soal)
              <div class="card col-md-4">
                <div class="card-header bg-white center">
                  <h4 class="card-title"><a href="{{ url('/soal-siswa/'.$soal->id) }}">{{ $soal->paket }}</a></h4>
                </div>
                <div class="card-block">
                  <p class="m-b-0" style="color: #a6aab2; font-size: 11pt">
                    {{ $soal->deskripsi }}
                  </p>
                  <hr>
                </div>
              </div>
            @endforeach
            @else
              <div class="alert alert-danger" style="margin-bottom: 0">
                Belum ada soal latihan.
              </div>
            @endif
            </div>
          </div>
        <?php } ?>
        <hr class="clearfix">
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-white">
        <div class="media">
          <?php if ($materi->gambar_user != "") { ?>
            <div class="media-left media-middle">
              <img src="{{ url('/img/'.$materi->gambar_user) }}" alt="img" width="50" class="img-circle">
            </div>
          <?php } ?>
          <div class="media-body media-middle">
            <h4 class="card-title"><a href="#">{{ Auth::user()->nama }}</a></h4>
            <p class="card-subtitle">
              <?php if ($materi->jenis_user == 'A') {
                echo "Admin";
              }else{
                echo "Guru";
              } ?>
            </p>
          </div>
        </div>
      </div>
      <div class="card-block">
        <!-- <p>Having over 12 years exp. Adrian is one of the lead UI designers in the industry Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, aut.</p> -->
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script>
$(document).ready(function(){
  $(".showGambar").click(function(){
    $(this).hide();
    $("#wrap-gambar").removeClass('hideGambar').hide().fadeIn(350);
  });
});
</script>
@endsection
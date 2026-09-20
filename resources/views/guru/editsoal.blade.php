@extends('layouts/guru_baru')
@section('title', 'Edit soal')
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
    <li><a href="{{ url('/soal-guru') }}">Soal</a></li>
    <li class="active">Edit Soal</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff"> Selamat Datang di Aplikasi Ujian Berbasis Komputer <b>{{ $school->nama }}</b> </div>
    <div class="panel panel-default">
      <div class="panel-heading">Ubah Paket Soal</div>
      <div class="panel-body">
        <div class="well" style="margin: 0; padding: 15px"> {!! Form::open(['url' => 'simpansiswa', 'class' => 'form-horizontal']) !!}
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="id_soal" id="id_soal" value="{{ $soal->id }}">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Paket</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="paket" id="paket" placeholder="Paket soal, misal: UTS KKPI Kelas XI" value="{!! $soal->paket !!}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Deskripsi</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="deskripsi" id="deskripsi" placeholder="Deskripsi">{!! $soal->deskripsi !!}</textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">KKM</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="kkm" id="kkm" placeholder="KKM, tuliskan dengan bilangan bulat" value="{!! $soal->kkm !!}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Waktu</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="waktu" id="waktu" placeholder="Waktu, tuliskan waktu dalam bentuk detik. Misal 60 menit, tuliskan 3600" value="{!! $soal->waktu !!}">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-primary" id="btnsimpansiswa">Simpan</button>
              <img src="{{ url('img/ajax-loader.gif') }}" alt="Loading" id="loading" style="display: none;"> </div>
          </div>
          <div class="alert alert-danger" id="salah" style="display: none;"></div>
          <div class="alert alert-info" id="benar" style="display: none;"><b>Sukses </b>Soal berhasil di perbarui. <i>Refresh</i> halaman untuk melihat data.</div>
          </form>
        </div>
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
      $("loading").hide();
      $("#salah").hide();
      $("#benar").hide();
      $('.collapse').collapse();

      $("#btnsimpansiswa").click(function() {
        $(this).hide();
        $("#loading").show();
        var paket = $("#paket").val();
        var deskripsi = $("#deskripsi").val();
        var kkm = $("#kkm").val();
        var waktu = $("#waktu").val();
        var id_soal = $("#id_soal").val();
        var datastring = "paket="+paket+"&deskripsi="+deskripsi+"&kkm="+kkm+"&waktu="+waktu+"&id_soal="+id_soal;
        $.ajax({
          type: "POST",
          url: "{{ url('/updateformsoal') }}",
          data: datastring,
          success: function(data){
            if(data == "berhasil"){
              $("#loading").hide();
              $("#salah").hide();
              $("#benar").show();
              $("#btnsimpansiswa").show();
              window.location.href = "{{ url('/soal-guru') }}"
            }else{
              $("#loading").hide();
              $("#benar").hide();
              $("#salah").html(data).show();
              $("#btnsimpansiswa").show();
            }
          }
        });
        return false;
      });
    });
</script>
@endsection
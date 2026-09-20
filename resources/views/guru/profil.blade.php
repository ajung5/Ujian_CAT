@extends('layouts/guru_baru')
@section('title', 'Profil')
@section('content')
<link href="{{ url('/lib/dropzone/dropzone.css') }}" rel="stylesheet">
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
    <li><a href="#">Home</a></li>
    <li class="active">Profil</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff"> Profil Sekolah </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-3 col-lg-3 wrap-data-sekolah" align="center">
          <?php if($school->logo != ""){ ?>
          <img alt="Sch Pic" src="{{ url('img/'.$school->logo) }}" width="150px" class="img-rounded img-thumbnail" style="margin-bottom: 15px">
          <?php }else{ ?>
          <img alt="Sch Pic" src="{{ url('img/noimage.jpg') }}" width="150px" class="img-rounded img-thumbnail" style="margin-bottom: 15px">
          <?php } ?>
        </div>
        <div class=" col-md-9 col-lg-9 wrap-data-sekolah">
          <table class="table table-user-information">
            <tbody>
              <tr>
                <td width="140px">Nama:</td>
                <td>{{ $school->nama }}</td>
              </tr>
              <tr>
                <td>Alamat:</td>
                <td>{{ $school->alamat }}</td>
              </tr>
              <tr>
                <td>Motto</td>
                <td>{!! $school->motto !!}</td>
              </tr>
            </tbody>
          </table>
          <button class="btn btn-primary" type="button" id="btn-ubah-sekolah">Ubah Data Sekolah</button>
        </div>
        <div id="wrap-ubah-sekolah" style="margin:0; display: none;">
          <div class="well" style="margin: 0;">
            <div id="formupdate" class="form-horizontal" style="margin: 0;">
              {!! csrf_field() !!}
              <div class="form-group">
                <label for="nama_sekolah" class="col-sm-2 control-label">Nama</label>
                <div class="col-sm-10">
                  <input type="text" name="nama_sekolah" id="nama_sekolah" class="form-control" value="{{ $school->nama }}">
                </div>
              </div>
              <div class="form-group">
                <label for="alamat_sekolah" class="col-sm-2 control-label">Alamat</label>
                <div class="col-sm-10">
                  <textarea type="text" name="alamat_sekolah" id="alamat_sekolah" class="form-control">{{ $school->alamat }}</textarea>
                </div>
              </div>
              <div class="form-group">
                <label for="motto_sekolah" class="col-sm-2 control-label">Motto</label>
                <div class="col-sm-10">
                  <textarea type="text" name="motto_sekolah" id="motto_sekolah" class="form-control">{{ $school->motto }}</textarea>
                </div>
              </div>
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Foto</label>
                <div class="col-sm-10">
                  <form action="{{ url('/upload-foto-sekolah') }}" class="dropzone">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" id="id_sekolah" name="id_sekolah" value="{{ $school->id }}">
                  <div class="fallback">
                    <input name="file_sekolah" type="file" multiple />
                  </div>
                </form>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="button" id="btnupdate-sekolah" class="btn btn-primary">Ubah</button>
                  <button type="button" id="batal-sekolah" class="btn btn-danger">Batal</button>
                  <img src="{{ url('/img/ajax-loader.gif') }}" alt="" id="loading-sekolah" style="display: none;">
                  <div id="notif-sekolah" style="display: none; margin-top: 15px"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff"> Data profil </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-3 col-lg-3 wrap-data-profil" align="center">
          <?php if($user->gambar != ""){ ?>
          <img alt="User Pic" src="{{ url('img/'.$user->gambar) }}" width="150px" class="img-rounded img-thumbnail" style="margin-bottom: 15px">
          <?php }else{ ?>
          <img alt="User Pic" src="{{ url('img/noimage.jpg') }}" width="150px" class="img-rounded img-thumbnail" style="margin-bottom: 15px">
          <?php } ?>
        </div>
        <div class=" col-md-9 col-lg-9 wrap-data-profil">
          <table class="table table-user-information">
            <tbody>
              <tr>
                <td width="140px">Nama:</td>
                <td>{{ $user->nama }}</td>
              </tr>
              <tr>
                <td>NIP:</td>
                <td>{{ $user->no_induk }}</td>
              </tr>
              <tr>
                <td>Jenis Kalamin</td>
                <td>
                  <?php
                    if ($user->jk == "L") {
                      echo "Laki-laki";
                    }else{
                      echo "Perempuan";
                    }
                  ?>
                </td>
              </tr>
              <tr>
                <td>Email</td>
                <td>{{ $user->email }}</td>
              </tr>
            </tbody>
          </table>
          <button class="btn btn-primary" type="button" id="btn-ubah">Ubah Profil</button>
        </div>
        <div id="wrap-ubah" style="margin:0; display: none;">
          <div class="well" style="margin: 0;">
            <div id="formupdate" class="form-horizontal" style="margin: 0;">
              {!! csrf_field() !!}
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Nama</label>
                <div class="col-sm-10">
                  <input type="text" name="nama" id="nama" class="form-control" value="{{ $user->nama }}">
                </div>
              </div>
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">NIP</label>
                <div class="col-sm-10">
                  <input type="text" name="nis" id="nis" class="form-control" value="{{ $user->no_induk }}">
                </div>
              </div>
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Jenis Kelamin</label>
                <div class="col-sm-10">
                  <select name="jk" id="jk" class="form-control">
                    <option value="{{ $user->jk }}">
                    <?php
                        if ($user->jk == "L") {
                          echo "Laki-laki";
                        }else{
                          echo "Perempuan";
                        }
                      ?>
                    </option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                  <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}">
                </div>
              </div>
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" name="password" id="password">
                </div>
              </div>
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Foto</label>
                <div class="col-sm-10">
                  <form action="{{ url('/upload-foto-user') }}" class="dropzone">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" id="id" name="id" value="{{ $user->id }}">
                  <div class="fallback">
                    <input name="file" type="file" multiple />
                  </div>
                </form>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="button" id="btnupdate" class="btn btn-primary">Ubah</button>
                  <button type="button" id="batal" class="btn btn-danger">Batal</button>
                  <img src="{{ url('/img/ajax-loader.gif') }}" alt="" id="loading" style="display: none;">
                  <div id="notif" style="display: none; margin-top: 15px"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{ url('/lib/dropzone/dropzone.js') }}"></script>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  $(document).ready(function() {
    'use strict';
    
    $("#btn-ubah-sekolah").click(function(){
      $(".wrap-data-sekolah").hide();
      $("#wrap-ubah-sekolah").fadeIn(350);
    });
    $("#batal-sekolah").click(function(){
      $("#wrap-ubah-sekolah").hide();
      $(".wrap-data-sekolah").fadeIn(350);
    });

    $("#btnupdate-sekolah").click(function() {
      $("#btnupdate-sekolah").hide();
      $("#batal-sekolah").hide();
      $("#loading-sekolah").show();
      var id = $("#id_sekolah").val();
      var nama_sekolah = $("#nama_sekolah").val();
      var alamat_sekolah = $("#alamat_sekolah").val();
      var motto_sekolah = $("#motto_sekolah").val();
      var datastring = "nama_sekolah="+nama_sekolah+"&alamat_sekolah="+alamat_sekolah+"&motto_sekolah="+motto_sekolah+"&id="+id;
      $.ajax({
        type: "POST",
        url: "{{ url('/update-profil-sekolah') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading-sekolah").hide();
            $("#notif-sekolah").removeClass('alert alert-danger').addClass('alert alert-info').html('Profil sekolah berhasil diupdate.').fadeIn(250);
            $("#btnupdate-sekolah").show();
            $("#batal-sekolah").show();
            window.location.href = "{{ url('/profil-guru') }}";
          }else{
            $("#loading-sekolah").hide();
            $("#notif-sekolah").removeClass('alert alert-info').addClass('alert alert-danger').html(data).fadeIn(250);
            $("#btnupdate-sekolah").show();
            $("#batal-sekolah").show();
          }
        }
      });
    });

    $("#btn-ubah").click(function(){
      $(".wrap-data-profil").hide();
      $("#wrap-ubah").fadeIn(350);
    });
    $("#batal").click(function(){
      $("#wrap-ubah").hide();
      $(".wrap-data-profil").fadeIn(350);
    });

    $("#btnupdate").click(function() {
      $("#btnupdate").hide();
      $("#batal").hide();
      $("#loading").show();
      var id = $("#id").val();
      var nama = $("#nama").val();
      var nis = $("#nis").val();
      var jk = $("#jk").val();
      var email = $("#email").val();
      var password = $("#password").val();
      var datastring = "nama="+nama+"&nis="+nis+"&jk="+jk+"&email="+email+"&password="+password+"&id="+id;
      $.ajax({
        type: "POST",
        url: "{{ url('/updateprofil') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#notif").removeClass('alert alert-danger').addClass('alert alert-info').html('Profil berhasil diupdate.').fadeIn(250);
            $("#btnupdate").show();
            $("#batal").show();
            window.location.href = "{{ url('/profil-guru') }}";
          }else{
            $("#loading").hide();
            $("#notif").removeClass('alert alert-info').addClass('alert alert-danger').html(data).fadeIn(250);
            $("#btnupdate").show();
            $("#batal").show();
          }
        }
      });
    });
  });
</script>
@endsection
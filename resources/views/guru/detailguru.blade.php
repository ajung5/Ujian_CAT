@extends('layouts/guru_baru')
@section('title', 'Detail Guru')
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
    <li><a href="{{ url('/guru') }}">Home</a></li>
    <li><a href="{{ url('/data-guru') }}">Data Guru</a></li>
    <li class="active">Detail Guru</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Detail Guru</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
        <?php if (Auth::user()->status == "A"){ ?>
          <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#wrapubah" aria-expanded="false" aria-controls="wrapubah">
            Ubah Data Guru
          </button>
          <a href="{{ url('hapusguru/'.$user->id) }}" title="Hapus Guru" class="btn btn-danger" onclick="return confirm('Yakin data akan dihapus?')">Hapus Guru</a>
          <div style="height: 20px"></div>
          <div class="collapse" id="wrapubah">
            <div class="well">
              <div method="POST" id="formupdate" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama</label>
                  <div class="col-sm-10">
                    <!-- <input type="hidden" name="id" id="id" value="{{ $user->id }}"> -->
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
                <div class="form-group" id="submit">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="btnupdate" class="btn btn-primary">Simpan</button>
                    <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="" id="loading" style="display: none;">
                    <div id="notif" style="display: none;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        </div>
        <div class="col-sm-4 col-md-4">
          <?php if ($user->gambar == "") { ?>
          <img src="{{ url('img/noimage.jpg') }}" alt="foto guru" class="img-rounded img-thumbnail" />
          <?php }else{ ?>
          <img src="{{ url('img/'.$user->gambar) }}" alt="{{$user->gambar}}" class="img-rounded img-thumbnail" />
          <?php } ?>
        </div>
        <div class="col-sm-8 col-md-8">
          <table class="table table-user-information">
            <tbody>
              <tr>
                <td width="140px">Nama:</td>
                <td>{{ $user->nama }}</td>
              </tr>
              <tr>
                <td>NIP:</td>
                <td><?php if ($user->no_induk != "") {
                  echo $user->no_induk;
                }else{
                  echo "-";
                  } ?></td>
              </tr>
              <tr>
                <td>Jenis Kalamin</td>
                <td><?php
                  if ($user->jk == "L") {
                    echo "Laki-laki";
                  }else{
                    echo "Perempuan";
                  }
                ?></td>
              </tr>
              <tr>
                <td>Email</td>
                <td>{{ $user->email }}</td>
              </tr>
            </tbody>
          </table>
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
    $("#btnupdate").click(function(){
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
            window.location.href = "{{ url('/detail-guru/'.$user->id) }}";
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
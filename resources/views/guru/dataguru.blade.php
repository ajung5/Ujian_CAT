@extends('layouts/guru_baru')
@section('title', 'Data Guru.')
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
    <li class="active">Data Guru</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Data Guru</div>
    <div class="panel-body">
      <?php if (Auth::user()->status == "A") { ?>
        <a href="#wrapsiswa" data-toggle="collapse">
          <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Tambah siswa melalui form.">Tambah Guru</button>
        </a>
        <div class="collapse" id="wrapsiswa" style="margin:15px 0 0 0;">
          <div class="well">
            {!! Form::open(['url' => 'simpansiswa', 'class' => 'form-horizontal']) !!}
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama Lengkap Guru">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">NIP</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="no_induk" id="no_induk" placeholder="NIP Guru">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="email" id="email" placeholder="Email Guru">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Jenis Kelamin</label>
                <div class="col-sm-10">
                  <select name="jk" id="jk" class="form-control">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" class="btn btn-primary" id="btnsimpan">Simpan</button>
                  <img src="img/ajax-loader.gif" alt="Loading" id="loading">
                </div>
              </div>
              <div class="alert alert-danger" id="salah"></div>
              <div class="alert alert-info" id="benar"><b>Sukses </b>Data guru berhasil disimpan. Guru dapat masuk ke sistem menggunakan email dan password: 123456</div>
            </form>
          </div>
        </div>
        <hr>
      <?php } ?>

      <div class="form-horizontal" style="margin-bottom: 15px">
        <input type="text" class="form-control" id="q" placeholder="Cari berdasarkan Nama (Ketik lalu enter)">
      </div>
      <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading" style="display: none;">
      <div class="table-responsive" id="wrap-user">
        <table class="table table-bordered table-default table-striped nomargin" id="users-table">
          <thead>
            <tr>
              <th style="text-align: center;">#</th>
              <th>Nama</th>
              <th>NIP</th>
              <th>Email</th>
              <th>J.Kelamin</th>
              <th width="50px" style="text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = $users->firstItem(); ?>
            @if($users->count())
            @foreach($users as $data)
            <?php
              if ($data->jk == 'L') {
                $jk = 'Laki-laki';
              }else{
                $jk = 'Perempuan';
              }
            ?>
            <tr>
              <td style="text-align: center;">{{ $no++ }}</td>
              <td>{{ $data->nama }}</td>
              <td>{{ $data->no_induk }}</td>
              <td>{{ $data->email }}</td>
              <td>{{ $jk }}</td>
              <td style="text-align: center;">
                <a href="{{ url('/detail-guru/'.$data->id) }}" class="btn btn-primary btn-xs" title="Detail">Detail</a>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="6" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
            @endif
          </tbody>
        </table>
        {!! $users->render() !!}
      </div>
    </div>
  </div>
</div>
<script src="{{url('lib/jquery/jquery.js')}}"></script>
<script src="{!! url('js/jquery.dataTables.min.js') !!}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(document).ready(function() {
    $("#q").keyup(function(e){
      if(e.keyCode == 13)
      {
        $("#loading").show();
        var q = encodeURIComponent($("#q").val());
        $.ajax({
          type: "POST",
          url: "{{ url('/get-user') }}",
          data: 'q='+q,
          success: function(data){
            $("#loading").hide();
            $("#wrap-user").hide().html(data).fadeIn(350);
          }
        })
      }
    });


    $("#loading").hide();
    $("#salah").hide();
    $("#benar").hide();

    $("#btnsimpan").click(function() {
      $(this).hide();
      $("#loading").show();
      var nama = $("#nama").val();
      var no_induk = $("#no_induk").val();
      var email = $("#email").val();
      var jk = $("#jk").val();
      var id_kelas = $("#id_kelas").val();

      var datastring = "nama="+nama+"&no_induk="+no_induk+"&email="+email+"&jk="+jk;
      $.ajax({
        type: "POST",
        url: "{{ url('/simpanformguru') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#salah").hide();
            $("#benar").show();
            $("#btnsimpan").show();
            location.reload();
          }else{
            $("#loading").hide();
            $("#benar").hide();
            $("#salah").html(data).show();
            $("#btnsimpan").show();
          }
        }
      });
      return false;
    });
  });
</script>
@endsection
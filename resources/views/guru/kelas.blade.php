@extends('layouts/guru_baru')
@section('title', 'Kelas')
@section('content')
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
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
    <li class="active">Kelas</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Data Kelas</div>
    <div class="panel-body">
      <div class="alert alert-warning"><span class="fa fa-exclamation-circle"></span> <b>PERHATIAN: </b>Merubah atau menghapus data kelas bisa berdampak buruk terhadap siswa yang terdapat didalamnya.</div>
        <a href="#" class="btn btn-primary" data-toggle="collapse" id="btnwrap" data-target="#wrapubah"><i class="fa fa-pencil-square-o"></i> Tambah Kelas</a>
        <div class="collapse" id="wrapubah" style="margin:15px 0 0 0;">
          <div class="well">
            <form method="POST" id="formupdate" class="form-horizontal">
              {!! csrf_field() !!}
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Nama Kelas</label>
                <div class="col-sm-10">
                  <input type="hidden" name="id_user" id="id_user" value="{{ Auth::user()->id }}">
                  <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama kelas">
                </div>
              </div>
              <div class="form-group" id="submit">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="button" id="btntbhkelas" class="btn btn-success">Simpan</button>
                </div>
              </div>
              <img src="{{ url('/img/ajax-loader.gif') }}" alt="" id="loading" style="display: none;">
              <div id="notif" style="display: none;"></div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-default table-striped nomargin" style="margin:15px 0 0 0;">
            <thead>
              <tr>
                <td align="center"><b>No</b></td>
                <td align="center"><b>ID.Kelas</b></td>
                <td align="center"><b>Nama Kelas</b></td>
                <td align="center"><b>Jumlah Siswa</b></td>
                <td align="center"><b>Aksi</b></td>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              @if($kelas->count())
              @foreach($kelas as $daftarkelas)
              <tr id="baris{{ $daftarkelas->id }}">
                <td align="center" width="45px">{{ $no++ }}</td>
                <td align="center">{{ $daftarkelas->id }}</td>
                <td>
                  <span style="cursor: pointer;" id="wrap-nama{{ $daftarkelas->id }}">{{ $daftarkelas->nama }}</span>
                  <input type="hidden" name="id" id="id{{ $daftarkelas->id }}" value="{{ $daftarkelas->id }}">
                  <input type="text" id="nama{{ $daftarkelas->id }}" class="form-control" value="{{ $daftarkelas->nama }}" style="display: none;">
                  <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading{{ $daftarkelas->id }}" style="display: none;">
                </td>
                <td align="center" width="175px"><?php
                  $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
                  $sql_siswa = $conn->query("SELECT COUNT(*) AS total FROM users WHERE id_kelas = '$daftarkelas->id' AND status='S'");
                  while($row_siswa = $sql_siswa->fetch_assoc()){
                    echo "<b>".$row_siswa['total']."<b>";
                  }
                  $conn->close();
                ?></td>
                <td width="90px" align="center">
                  <a href="#" id="hapus{{ $daftarkelas->id }}" data-toggle="tooltip" title="Hapus kelas akan membuat data siswa didalamnya tidak memiliki kelas." class="btn btn-danger btn-xs"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  <a href="{{ url('/detail-kelas', $daftarkelas->id) }}" data-toggle="tooltip" title="Detail kelas berisi daftar siswa didalamnya." class="btn btn-primary btn-xs"><i class="fa fa-search" aria-hidden="true"></i></a></td>
              </tr>
              <script>
                $(document).ready(function(){
                  $("#wrap-nama{{ $daftarkelas->id }}").click(function(){
                    $("#wrap-nama{{ $daftarkelas->id }}").hide();
                    $("#nama{{ $daftarkelas->id }}").fadeIn(350).focus();
                  });
                  $("#nama{{ $daftarkelas->id }}").keyup(function(e){
                    if(e.keyCode == 13)
                    {
                      $("#nama{{ $daftarkelas->id }}").hide();
                      $("#loading{{ $daftarkelas->id }}").show();
                      var id = $("#id{{ $daftarkelas->id }}").val();
                      var nama = $("#nama{{ $daftarkelas->id }}").val();
                      $.ajax({
                        type: "POST",
                        url: "{{ url('/ajax/ubah-kelas') }}",
                        data: "nama="+nama+'&id='+id,
                        success: function(data){
                          $("#loading{{ $daftarkelas->id }}").hide();
                          $("#wrap-nama{{ $daftarkelas->id }}").html(data).fadeIn(350);
                        }
                      })
                    }
                  });

                  $("#hapus{{ $daftarkelas->id }}").click(function(){
                    if (confirm('Yakin data akan dihapus?')) {
                       var id_kelas = $("#id{{ $daftarkelas->id }}").val();
                      var datastring = "id_kelas="+id_kelas;
                      $.ajax({
                        type: "POST",
                        url: "{{ url('/hapuskelas') }}",
                        data: datastring,
                        success: function(data){
                          if(data == "berhasil"){
                            $("#baris{{ $daftarkelas->id }}").hide();
                          }else{
                            alert('Gagal menghapus data');
                          }
                        }
                      });
                    }
                    return false;
                  });
                });
              </script>
              @endforeach
              @endif
            </tbody>
          </table>
          {!! $kelas->render() !!}
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
<script>
  $(document).ready(function() {
    $("#btntbhkelas").click(function() {
      $("#loading").show();
      var nama = encodeURIComponent($("#nama").val());
      var id_user = $("#id_user").val();
      var datastring = "nama="+nama+"&id_user="+id_user;
      $.ajax({
        type: "POST",
        url: "{{ url('/tambahkelas') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#notif").removeClass('alert alert-danger').addClass('alert alert-info').html(data).show();
            window.location.href = "{{ url('/kelas') }}";
          }else{
            $("#loading").hide();
            $("#notif").removeClass('alert alert-info').addClass('alert alert-danger').html(data).show();
          }
        }
      });
    });
  });
</script>
@endsection
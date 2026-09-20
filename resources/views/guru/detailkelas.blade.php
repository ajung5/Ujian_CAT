@extends('layouts/guru_baru')
@section('title', 'Detail Kelas')
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
<div class="col-md-12 dash-left">
  <ol class="breadcrumb">
    <li><a href="{{ url('/guru') }}">Home</a></li>
    <li><a href="{{ url('/kelas') }}">Kelas</a></li>
    <li class="active">{{ $kelassiswa->nama }}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading"> Daftar siswa kelas <b>{{ $kelassiswa->nama }}</b> </div>
      <div class="panel-body">
        <div class="alert alert-warning"><span class="fa fa-exclamation-circle"></span> <b>PERHATIAN: </b>Anda dapat menambah, merubah atau menghapus data siswa untuk kelas ini ({{ $kelassiswa->nama }}). Isikan data dengan benar untuk mendapatkan hasil ujian yang tepat.</div>
        <a href="#" class="btn btn-primary" data-toggle="collapse" id="btnwrap" data-target="#wrapubah"><i class="fa fa-pencil-square-o"></i> Tambah Siswa</a>
        <div class="collapse" id="wrapubah" style="margin:15px 0 0 0;">
          <div class="well">
            <form method="POST" id="formupdate" class="form-horizontal">
              {!! csrf_field() !!}
              <div class="form-group">
                <label for="nama" class="col-sm-2 control-label">Nama Siswa</label>
                <div class="col-sm-10">
                  <input type="hidden" name="id_user" id="id_user" value="{{ Auth::user()->id }}">
                  <input type="hidden" name="id_kelas" id="id_kelas" value="{{ $kelassiswa->id }}">
                  <select class="form-control" style="width:100%" id="siswa">
                    <option value="">-- Pilih Siswa --</option>
                    @if($calonsiswas->count())
                    @foreach($calonsiswas as $calonsiswa)
                      <option value="{{ $calonsiswa->id }}">{{ $calonsiswa->no_induk." - ".$calonsiswa->nama }}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="form-group" style="display: none; margin: 0; padding: 0;" id="wrapinfo">
                <div class="col-sm-offset-2 col-sm-10 alert alert-success" id="info"></div>
              </div>
              <div class="form-group" id="submit">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="button" id="btntbhsiswa" class="btn btn-primary">Simpan</button>
                  <img src="{{ url('/img/ajax-loader.gif') }}" alt="" id="loaderupdate" style="margin:5px 0 0 0; display: none;">
                </div>
              </div>
              <div class="col-sm-offset-2 col-sm-10 alert alert-success" id="updatebenar" style="display: none;">Data siswa berhasil dipindah kelas. <b><i>Refresh</i></b> halaman untuk melihat perubahannya.</div>
              <div class="col-sm-offset-2 col-sm-10 alert alert-danger" id="updatesalah" style="display: none;"></div>
              <div class="clearfix"></div>
            </form>
          </div>
        </div>
        <div style="overflow-x: scroll;">
          <table class="table table-condensed table-hover table-bordered" style="margin:15px 0 0 0;">
            <thead>
              <tr>
                <td align="center"><b>No</b></td>
                <td align="center"><b>Nama</b></td>
                <td align="center"><b>NIS</b></td>
                <td align="center"><b>J.kelamin</b></td>
                <td align="center"><b>Email</b></td>
                <td align="center"><b>Aksi</b></td>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
            @if($siswas->count())
            @foreach($siswas as $siswa)
            <input type="hidden" name="id_siswa{{ $siswa->id }}" id="id_siswa{{ $siswa->id }}" value="{{ $siswa->id }}">
            <tr id="baris{{ $siswa->id }}">
              <td align="center" width="45px">{{ $no++ }}</td>
              <td>{{ $siswa->nama }}</td>
              <td>{{ $siswa->no_induk }}</td>
              <td><?php
                if($siswa->jk == "L"){
                  echo "Laki-laki";
                }else{
                  echo "Perempuan";
                }
              ?></td>
              <td>{{ $siswa->email }}</td>
              <td width="120px" align="center"><a href="#" id="hapus{{ $siswa->id }}" data-toggle="tooltip" title="Hapus siswa dari kelas ini, akan membuat siswa tidak memiliki kelas">Hapus</a> | <a href="{{ url('/detail-kelas-siswa', $siswa->id) }}" data-toggle="tooltip" title="Detail data siswa. Melihat histori ujian yang pernah diakukan oleh siswa tersebut.">Detail</a></td>
            </tr>
            <script>
              $(document).ready(function() {
                $("#hapus{{ $siswa->id }}").click(function(){
                  if (confirm('Siswa yang dihapus dari kelas akan kehilangan kelas.')) {
                    var id_siswa = $("#id_siswa{{ $siswa->id }}").val();
                    var datastring = "id_siswa="+id_siswa;
                    $.ajax({
                      type: "POST",
                      url: "{{ url('/hapuskelassiswa') }}",
                      data: datastring,
                      success: function(data){
                        if(data == "berhasil"){
                          $("#baris{{ $siswa->id }}").hide();
                          /*alert('Data berhasil dihapus. Refresh halaman untuk melihat perubahan.');*/
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
            @else
            <tr>
              <td colspan="6"><div class="alert alert-danger"><b>Upsss:</b> Data siswa untuk kelas ini ({{ $kelas_siswa }}) masih kosong...</div></td>
            </tr>
            @endif
            </tbody>
          </table>
        </div>
      </div>
  </div>
</div>
<script>
$(document).ready(function() {
  $("#wrapinfo").hide();
  $("#siswa").click(function() {
    $("#wrapinfo").hide();
  });
  $("#siswa").change(function() {
    var siswa = $("#siswa").val();
    var datastring = "siswa="+siswa;
        $.ajax({
          type: "POST",
          url: "{{ url('/cekkelassiswa') }}",
          data: datastring,
          success: function(data){
            $("#wrapinfo").fadeIn();
            $("#info").html(data).show();
          }
        });
  });

  $("#btntbhsiswa").click(function() {
    $("#wrapinfo").hide();
    /*$("#btntbhsiswa").hide();*/
    $("#updatesalah").hide();
    $("#loaderupdate").show();
    var siswa = $("#siswa").val();
    var id_kelas = $("#id_kelas").val();
    var datastring = "siswa="+siswa+"&id_kelas="+id_kelas;
      $.ajax({
        type: "POST",
        url: "{{ url('/tambahsiswakekelas') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loaderupdate").hide();
            $("#updatesalah").hide();
            $("#updatebenar").show();
            $("#btntbhsiswa").show();
            window.location.href = "{{ url('/detail-kelas/'.$kelassiswa->id) }}";
          }else{
            $("#loaderupdate").hide();
            $("#updatebenar").hide();
            $("#btntbhsiswa").show();
            $("#updatesalah").html(data).show();
            $("#btntbhsiswa").show();
          }
        }
      });
  });
});
</script>
@endsection
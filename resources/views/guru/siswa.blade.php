@extends('layouts/guru_baru')
@section('title', 'Data Siswa')
@section('content')
<link rel="stylesheet" href="{{ url('css/upload.css') }}">
<script src="{{url('lib/jquery/jquery.js')}}"></script>
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
    <li class="active">Data Siswa</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Data Siswa</div>
    <div class="panel-body">
      <a href="#wrapsiswa" data-toggle="collapse"><button type="button" class="btn btn-primary" data-toggle="tooltip" title="Tambah siswa melalui form.">Tambah</button></a>
      <a href="{{ URL::to( '/readfile/data.xls')  }}" target="_blank"><button type="button" class="btn btn-success" data-toggle="tooltip" title="Download format excel untuk mengupload data siswa."><i class="fa fa-download" aria-hidden="true"></i> Excel</button></a>
      <button type="button" class="btn btn-success" data-toggle="collapse" href="#uploadexcel" aria-expanded="false"><i class="fa fa-upload" aria-hidden="true"></i> Siswa via Excel</button>

      <a href="{{ URL::to( '/readfile/datacalon.xls')  }}" target="_blank"><button type="button" class="btn btn-success" data-toggle="tooltip" title="Download format excel untuk mengupload data calon siswa."><i class="fa fa-download" aria-hidden="true"></i> Excel Calon Siswa</button></a>
      <button type="button" class="btn btn-success" data-toggle="collapse" href="#uploadexcelcalonsiswa" aria-expanded="false"><i class="fa fa-upload" aria-hidden="true"></i> Calon Siswa via Excel</button>

      <a href="#" disabled><button type="button" class="btn btn-danger" data-toggle="tooltip" title="Seluruh Siswa Akan Hilang Secara Permanent" disabled>Hapus Seluruh Siswa</button></a>
      <a href="{{ url('/hapuscalonsiswa') }}" onclick="return confirm('Yakin seluruh data peserta PSB akan dihapus? Data yang dihapus tidak dapat dikemalikan. Hasil ujian siswa juga akan ikut terhapus. Tetap melanjutkan?')"><button type="button" class="btn btn-danger" data-toggle="tooltip" title="Seluruh Calon Siswa Akan Hilang Secara Permanent">Hapus Seluruh Calon Siswa</button></a>

      <div class="collapse in" id="wrapsiswa" style="margin:15px 0 0 0;">
        <div class="well">
          {!! Form::open(['url' => 'simpansiswa', 'class' => 'form-horizontal']) !!}
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">NIS</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="no_induk" id="no_induk" placeholder="NIS">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
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
              <label for="inputEmail3" class="col-sm-2 control-label">Kelas</label>
              <div class="col-sm-10">
                <select name="id_kelas" id="id_kelas" class="form-control">
                  <option value="">-- Pilih Kelas --</option>
                  @if($kelas->count())
                  @foreach($kelas as $daftarkelas)
                  <option value="{{ $daftarkelas->id }}">{{ $daftarkelas->nama }}</option>
                  @endforeach
                  @endif
                </select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary" id="btnsimpansiswa">Simpan</button>
                <img src="img/ajax-loader.gif" alt="Loading" id="loading" style="display: none;">
              </div>
            </div>
            <div class="alert alert-danger" id="salah" style="display: none;"></div>
            <div class="alert alert-info" id="benar" style="display: none;"><b>Sukses </b>Data siswa berhasil disimpan. Siswa dapat masuk ke sistem menggunakan email dan password: 123456</div>
          </form>
        </div>
      </div>

      <div class="collapse in" id="uploadexcel" style="margin:15px 0 0 0;">
        <div class="well">
          {!! Form::open(['url' => 'uploadsiswa', 'class' => 'form-horizontal', 'files' => true]) !!}
          
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">File (Exel)</label>
              <div class="col-sm-10">
                <input type="file" name="file" id="file" class="inputfile" required />
                <label for="file"><strong><span class="fa fa-cloud-upload"></span> Choose a file</strong></label>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Upload</button>
              </div>
            </div>
            <div class="alert alert-danger"><b>PERHATIAN:</b> Isikan dengan benar data siswa kedalam format excel yang disediakan. Jangan menambah atau menghapus <i>column</i> yang ada di format excel. NIS dan email harus unik, sistem akan menolak apabila NIS atau email tersebut sudah terdaftar di database.</div>
          </form>
        </div>
      </div>

      <div class="collapse in" id="uploadexcelcalonsiswa" style="margin:15px 0 0 0;">
        <div class="well">
          {!! Form::open(['url' => 'uploadcalonsiswa', 'class' => 'form-horizontal', 'files' => true]) !!}
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">File (Exel) Calon</label>
              <div class="col-sm-10">
                <input type="file" name="filecalon" id="filecalon" class="inputfile" required />
                <label for="filecalon"><strong><span class="fa fa-cloud-upload"></span> Choose a file</strong></label>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Upload</button>
              </div>
            </div>
            <div class="alert alert-danger"><b>PERHATIAN:</b> Isikan dengan benar data calon siswa kedalam format excel yang disediakan. Jangan menambah atau menghapus <i>column</i> yang ada di format excel. ID Pendaftaran dan email harus unik, sistem akan menolak apabila ID Pendaftaran atau email tersebut sudah terdaftar di database.</div>
          </form>
        </div>
      </div>
      <hr>
      <div class="form-horizontal" style="margin-bottom: 15px">
        <input type="text" class="form-control" id="q" placeholder="Cari berdasarkan Nama (Ketik lalu enter)">
      </div>
      <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading_cari" style="display: none;">
      <div id="wrap-user" class="table-responsive">
        Jumlah siswa: {{ $jumlah_siswa->count() }}
        <table class="table table-bordered table-default table-striped nomargin" id="users-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>NIS</th>
              <th>Email</th>
              <th>J.Kelamin</th>
              <th>Kelas</th>
              <th width="50px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = $users->firstItem(); ?>
            @if($users->count())
            @foreach($users as $data_user)
            <?php
              if ($data_user->jk == 'L') {
                $jk = 'Laki-laki';
              }else{
                $jk = 'Perempuan';
              }
            ?>
            <tr>
              <td>{{ $no++ }}</td>
              <td>{{ $data_user->nama }}</td>
              <td>{{ $data_user->no_induk }}</td>
              <td>{{ $data_user->email }}</td>
              <td>{{ $jk }}</td>
              <td>{{ $data_user->nama_kelas }}</td>
              <td>
                <a href="{{ url('/detail-kelas-siswa/'.$data_user->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Detail</a>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="7" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
            @endif
          </tbody>
        </table>
        {!! $users->render() !!}
      </div>
    </div>
  </div>
</div>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
  $(document).ready(function() {
    $("#q").keyup(function(e){
      if(e.keyCode == 13)
      {
        $("#loading_cari").show();
        var q = encodeURIComponent($("#q").val());
        $.ajax({
          type: "POST",
          url: "{{ url('/get-siswa') }}",
          data: 'q='+q,
          success: function(data){
            $("#loading_cari").hide();
            $("#wrap-user").hide().html(data).fadeIn(350);
          }
        })
      }
    });

    $("#loading").hide();
    $("#salah").hide();
    $("#benar").hide();
    $('.collapse').collapse();
    $("#btnsimpansiswa").click(function() {
      $(this).hide();
      $("#loading").show();
      var nama = $("#nama").val();
      var no_induk = $("#no_induk").val();
      var email = $("#email").val();
      var jk = $("#jk").val();
      var id_kelas = $("#id_kelas").val();
      var datastring = "nama="+nama+"&no_induk="+no_induk+"&email="+email+"&jk="+jk+"&id_kelas="+id_kelas;
      $.ajax({
        type: "POST",
        url: "{{ url('/simpanformsiswa') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#salah").hide();
            $("#benar").show();
            $("#btnsimpansiswa").show();
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
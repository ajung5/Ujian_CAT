@extends('layouts/guru_baru')
@section('title', 'Detail Kelas Siswa')
@section('content')
<style type="text/css" media="screen">
  h1{
    text-align: center;
    background-color: #FEFFED;
    height: 70px;
    color: rgb(95, 89, 89);
    margin: 0 0 -29px 0;
    padding-top: 14px;
    border-radius: 10px 10px 0 0;
    font-size: 35px;
  }
  #image_preview{
    font-size: 30px;
    width: 100%;
    text-align: center;
    font-weight: bold;
    color: #C0C0C0;
    background-color: #FFFFFF;
    overflow: auto;
    padding: 15px 0;
  }
  #selectImage{
    padding: 19px 21px 14px 15px;
    width: 100%;
    background-color: #FEFFED;
    border-radius: 10px;
  }
  #loading{
    display:none;
    font-size:25px;
    margin: 15px 0 0 0;
  }
  #message{
    margin: 15px 0 0 0;
  }
  .inputfile {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
  }
  .inputfile + label {
      font-size: 1.25em;
      font-weight: 700;
      color: white;
      background-color: #444443;
      display: inline-block;
      cursor: pointer;
      padding: 10px;
  }
  .inputfile:focus + label,
  .inputfile + label:hover {
      background-color: red;
  }
  .inputfile:focus + label {
    outline: 1px dotted #000;
    outline: -webkit-focus-ring-color auto 5px;
  }
  #success {
    color:green;
  }
  #invalid {
    color:red;
  }
  #error {
    color:red;
  }
  #error_message {
    color:blue;
  }
</style>
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
    <li><a href="{{ url('/data-siswa') }}">Siswa</a></li>
    <li class="active">Detail Siswa</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Detail Siswa</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
          <button id="hapus" type="button" class="btn btn-danger" data-toggle="tooltip" title="Siswa yang telah dihapus tidak akan bisa dikembalikan, seluruh data siswa akan hilang dari sistem. Termasuk data ujian siswa.">Hapus Siswa</button>
          <hr>
        </div>
        <div class="col-md-3 col-lg-3" align="center">
          <?php
            if($siswa->gambar != ""){
              $gambar = $siswa->gambar;
          ?>
          <img alt="User Picture" src="../img/{{ $gambar }}" width="150px" class="img-rounded img-responsive">
          <?php
            }else{
          ?>
          <img alt="User Pic" src="../img/noimage.jpg" width="150px" class="img-rounded img-responsive">
          <?php
            }
          ?>
          <br>
          <button type="button" class="btn btn-info"  data-toggle="collapse" id="btnwrapfoto" data-target="#wrapubahfoto">Ubah Foto</button>
        </div>
        <div class=" col-md-9 col-lg-9 ">
          <div class="collapse" id="wrapubahfoto" style="margin:15px 0 0 0;">
            <div class="well">
              <div class="main">
                <h1>Ubah foto profil</h1>
                <hr>
                <form id="uploadimagesiswa" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                  <div id="image_preview"> <img id="previewing" src="{{ url('/img/noimage.jpg') }}" /> </div>
                  <hr id="line">
                  <div id="selectImage">
                    <input type="hidden" name="id_siswa" value="{{ $siswa->id }}" id="id_siswa">
                    <input type="file" name="file" id="file" class="inputfile" required />
                    <label for="file"><strong><span class="fa fa-cloud-download"></span> Choose a file</strong></label>
                    <input type="submit" value="Upload" class="submit btn btn-primary" />
                  </div>
                </form>
              </div>
              <h4 id='loading' >loading..</h4>
              <div id="message" class="alert"></div>
            </div>
          </div>
          <table class="table table-user-information">
            <tbody>
              <tr>
                <td width="140px">Nama:</td>
                <td>{{ $siswa->nama }}</td>
              </tr>
              <tr>
                <td>NIS:</td>
                <td>{{ $siswa->no_induk }}</td>
              </tr>
              <tr>
                <td>Kelas</td>
                <td>{{ $siswa->nama_kelas }}</td>
              </tr>
              <tr>
                <td>Jenis Kalamin</td>
                <td><?php
                  if ($siswa->jk == "L") {
                    echo "Laki-laki";
                  }else{
                    echo "Perempuan";
                  }
                ?></td>
              </tr>
              <tr>
                <td>Email</td>
                <td>{{ $siswa->email }}</td>
              </tr>
            </tbody>
          </table>
          <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#wrapubah">Ubah Profil</button>
          <!-- <a href="#" class="btn btn-primary" data-toggle="collapse" id="btnwrap" data-target="#wrapubah">Ubah Profil</a> -->
          <div class="collapse" id="wrapubah" style="margin:15px 0 0 0;">
            <div class="well">
              <form method="POST" id="formupdate" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama</label>
                  <div class="col-sm-10">
                    <input type="hidden" name="id_siswa" value="{{ $siswa->id }}" id="id_siswa">
                    <input type="text" name="nama" id="nama" class="form-control" value="{{ $siswa->nama }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">NIS</label>
                  <div class="col-sm-10">
                    <input type="text" name="nis" id="nis" class="form-control" value="{{ $siswa->no_induk }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Jenis Kelamin</label>
                  <div class="col-sm-10">
                    <select name="jk" id="jk" class="form-control">
                      <option value="{{ $siswa->jk }}">
                      <?php
                        if ($siswa->jk == "L") {
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
                    <input type="email" name="email" id="email" class="form-control" value="{{ $siswa->email }}" disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="password">
                  </div>
                </div>
                <div class="form-group" id="submit">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="btnupdatesiswa" class="btn btn-primary">Ubah</button>
                  </div>
                </div>
                <img src="{{ url('/img/ajax-loader.gif') }}" alt="" id="loaderupdate">
                <div class="alert alert-success" id="updatebenar">Data profil berhasil diupdate</div>
                <div class="alert alert-danger" id="updatesalah"></div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-12" style="margin:10px 0 0 0; overflow-x: scroll;">
          <hr style="margin: 20px 0 10px 0;">
          <table class="table table-bordered table-condensed">
            <caption>
            Daftar Ujian {{ $siswa->nama }} untuk materi Anda.
            </caption>
            <thead>
              <tr>
                <th>NO</th>
                <th>Paket</th>
                <th>KKM</th>
                <th>Durasi</th>
                <th>Tanggal Ujian</th>
                <th>Score</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
            
            @if($jawabs->count())
            <?php $no = $jawabs->firstItem(); ?>
            @foreach($jawabs as $jawab)
            <?php
              $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
              if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
              }
              $sql_soal = "SELECT * FROM soals WHERE id='$jawab->id_soal'";
              $result_soal = $conn->query($sql_soal);
              if($result_soal->num_rows > 0){
                while($row_soal = $result_soal->fetch_assoc()){
                  $tanggalawal = explode(" ", $jawab->updated_at);
                  $tanggal = explode("-", $tanggalawal[0]);
            ?>
            <tr>
              <td align="center">{{ $no++ }}</td>
              <td>{{ $row_soal['paket'] }}</td>
              <td>{{ $row_soal['kkm'] }}</td>
              <td><?php $waktu = $row_soal['waktu'] / 60; echo $waktu." menit"; ?></td>
              <td width="200" align="center">{{ $tanggal[2].' '.$bulan[$tanggal[1]].' '.$tanggal[0].' | '.$tanggalawal[1] }}</td>
              <td align="center"><?php
                $sql_nilai = "SELECT SUM(score) as nilai FROM jawabs WHERE id_soal='$row_soal[id]' AND id_user='$siswa->id'";
                $result_nilai = $conn->query($sql_nilai);
                while($row_nilai = $result_nilai->fetch_assoc()){
                  $nilai = $row_nilai['nilai'];
                  if ($nilai >= $row_soal['kkm']) {
                    echo "<span style='color:#2db300; font-size: 14px; font-weight:bold;'>".$nilai."</span>";
                  }else{
                    echo "<span style='color:#cc0000; font-size: 14px; font-weight:bold;'>".$nilai."</span>";
                  }
                }
              ?></td>
              <td align="center"><a href="#" title="Detail" id="tampildetailjawab{{ $jawab->id }}">Detail</a></td>
            </tr>
            <tr id="detailjawab{{ $jawab->id }}">
              <td colspan="7">
                <div class="wells" style="background:#5c6c84">
                  <table class="table">
                    <caption style="padding: 10px; color: #fff">
                      Rincian soal ujian untuk paket soal <b>{{ $row_soal['paket'] }}</b>
                    </caption>
                    <thead>
                      <tr>
                        <th>NO</th>
                        <th>Soal</th>
                        <th style="text-align: center">Kunci</th>
                        <th style="text-align: center">Jawab</th>
                        <th style="text-align: center">Score</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);

                        if ($conn->connect_error) {
                          die("Connection failed: " . $conn->connect_error);
                        }
                        $sql = "SELECT DISTINCT * FROM jawabs WHERE id_soal = '$jawab->id_soal' AND id_user = '$siswa->id' AND status='Y'";
                        $result = $conn->query($sql);
                        $no = 1;
                        if ($result->num_rows > 0) {
                          while($row = $result->fetch_assoc()) {
                            $sql_soal = $conn->query("SELECT DISTINCT * FROM detailsoals WHERE id='$row[no_soal_id]'");
                          while($row_soal = $sql_soal->fetch_assoc()){
                      ?>
                      <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row_soal['soal'] }}</td>
                        <td style="text-align: center">{{ $row_soal['kunci'] }}</td>
                        <td style="text-align: center">{{ $row['pilihan'] }}</td>
                        <td style="text-align: center">{{ $row['score'] }}</td>
                      </tr>
                      <?php
                            }
                          }
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
            <script>
              $(document).ready(function() {
                $("#detailjawab{{ $jawab->id }}").hide();
                $("#tampildetailjawab{{ $jawab->id }}").click(function(){
                  $("#detailjawab{{ $jawab->id }}").toggle();
                  return false;
                });
              });
            </script>
            <?php
                }
              }else{

              }
            ?>
            @endforeach
            @else
            <tr>
              <td colspan="7"><div class="alert alert-danger"><b>{{ $siswa->nama }}</b> belum pernah mengejakan soal ujian dari Anda.</div></td>
            </tr>
            @endif
            </tbody>
          </table>
          {!! str_replace('/?', '?', $jawabs->render()) !!} </div>
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
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$(document).ready(function() {
  $("#loaderupdate").hide();
  $("#updatebenar").hide();
  $("#updatesalah").hide();

  $("#btnupdatesiswa").click(function() {
    $("#loaderupdate").show();
    var id_siswa = $("#id_siswa").val();
    var nama = $("#nama").val();
    var nis = $("#nis").val();
    var jk = $("#jk").val();
    var password = $("#password").val();
    var datastring = "nama="+nama+"&nis="+nis+"&jk="+jk+"&password="+password+"&id_siswa="+id_siswa;
    $.ajax({
      type: "POST",
      url: "{{ url('/updateprofilsiswa') }}",
      data: datastring,
      success: function(data){
        if(data == "berhasil"){
          $("#loaderupdate").hide();
          $("#updatesalah").hide();
          $("#updatebenar").show();

          $("#submit").hide();
        }else{
          $("#loaderupdate").hide();
          $("#updatebenar").hide();
          $("#updatesalah").html(data).show();
        }
      }
    });
    return false;
  });

  $("#hapus").click(function() {
    if (confirm('Yakin data akan dihapus? Data yang telah dihapus tidak bisa dikembalikan.')) {
      var id_siswa = $("#id_siswa").val();
      var datastring = "id_siswa="+id_siswa;
      $.ajax({
        type: "POST",
        url: "{{ url('/hapussiswa') }}",
        data: datastring,
        success: function(data){
          if (data == "berhasil") {
            window.location.replace("{{ url('data-siswa') }}");
          };
        }
      });
    }
  });
});
$(document).ready(function (e) {
  $("#uploadimagesiswa").on('submit',(function(e) {
    e.preventDefault();
    $("#message").empty();
    $('#loading').show();
    $.ajax({
      url: "{{ url('/updateprofilfotosiswa') }}",
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData:false,
      success: function(data)
      {
        $('#loading').hide();
        $("#message").html(data);
      }
    });
  }));

  // Function to preview image after validation
  $(function() {
    $("#file").change(function() {
      $("#message").empty(); // To remove the previous error message
      var file = this.files[0];
      var imagefile = file.type;
      var match= ["image/jpeg","image/png","image/jpg"];
      if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
      {
        $('#previewing').attr('src','noimage.png');
        $("#message").html("<p id='error'>Harap gunakan jenis gambar yang valid</p>"+"<h4>Catatan</h4>"+"<span id='error_message'>Hanya jpeg, jpg dan png saja type yang di anggap valid</span>");
        return false;
      }else{
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[0]);
      }
    });
  });
  function imageIsLoaded(e) {
    $("#file").css("color","green");
    $('#image_preview').css("display", "block");
    $('#previewing').attr('src', e.target.result);
    $('#previewing').attr('width', '250px');
    $('#previewing').attr('height', '230px');
  };     
});
</script>
@endsection
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{{ url('img/favicon.png') }}">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>Detail Soal - Ujian tipa.co.id</title>
<style type="text/css" media="screen">
  .state-icon {
    left: -5px;
  }
  .list-group-item-primary {
    color: rgb(255, 255, 255);
    background-color: rgb(66, 139, 202);
  }
  /* DEMO ONLY - REMOVES UNWANTED MARGIN */
  .well .list-group {
    margin-bottom: 0px;
  }
  h1 {
    text-align: center;
    background-color: #FEFFED;
    height: 70px;
    color: rgb(95, 89, 89);
    margin: 0 0 -29px 0;
    padding-top: 14px;
    border-radius: 10px 10px 0 0;
    font-size: 35px;
  }
  #image_preview {
    font-size: 30px;
    width: 100%;
    text-align: center;
    font-weight: bold;
    color: #C0C0C0;
    background-color: #FFFFFF;
    overflow: auto;
    padding: 15px 0;
  }
  #selectImage {
    padding: 19px 21px 14px 15px;
    width: 100%;
    background-color: #FEFFED;
    border-radius: 10px;
  }
  #loading {
    display:none;
    font-size:25px;
    margin: 15px 0 0 0;
  }
  #message {
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
  .inputfile:focus + label, .inputfile + label:hover {
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
  /*#line {
        margin-top: 274px;
      }*/
  #error {
    color:red;
  }
  #error_message {
    color:blue;
  }
</style>
<link rel="stylesheet" href="{{url('lib/select2/select2.css')}}">
<link rel="stylesheet" href="{{url('lib/summernote/summernote.css')}}">
<link rel="stylesheet" href="{{url('lib/fa/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{ url('css/admin.css') }}">
<script src="{{url('js/modernizr.js')}}"></script>
<link rel="stylesheet" href="{{url('lib/Hover/hover.css')}}">
<link rel="stylesheet" href="{{url('lib/weather-icons/css/weather-icons.css')}}">
<link rel="stylesheet" href="{{url('lib/jquery-toggles/toggles-full.css')}}">
<link rel="stylesheet" href="{{url('lib/morrisjs/morris.css')}}">
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ url('/assets/js/dropzone/dropzone.css') }}">
<script src="{{ url('/assets/js/dropzone/dropzone.js') }}"></script>

</head>
<body>
<?php include(app_path() . '/functions/koneksi.php'); $url = Request::segment(1); ?>
<header>
  <div class="headerpanel" style="background: #fcfdff">
    <div class="logopanel" style="background: #00050f">
      <h2><a href="{{ url('/guru') }}" style="color: #fff">Ujian</a></h2>
    </div>
    <div class="headerbar">
      <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
      <div class="header-right">
        <ul class="headermenu">
          <li>
            <div class="btn-group">
              <button type="button" class="btn btn-logged" data-toggle="dropdown">
                <?php
                  $namapendek = explode(" ", Auth::user()->nama);
                  echo $namapendek[0];
                ?>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a href="{{ url('/profil-guru') }}"><i class="fa fa-user"></i> Profil</a></li>
                <li><a href="{{ url('/auth/logout') }}"><i class="fa fa-sign-out"></i> Log Out</a></li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>
<section>
<?php
  if (Auth::user()->status != "S" or Auth::user()->status != "C") {
  include(app_path() . '/functions/koneksi.php');
?>
  <div class="leftpanel">
    <div class="leftpanelinner">
      <div class="media leftpanel-profile">
        <div class="media-left">
          <a href="#">
            <?php if ($user->gambar == "") { ?>
            <img src="{{ url('img/guru.png') }}" alt="foto guru" class="media-object img-thumbnail" />
            <?php }else{ ?>
            <img src="{{ url('img/'.$user->gambar) }}" alt="{{$user->gambar}}" class="media-object img-thumbnail" />
            <?php } ?>
          </a>
        </div>
        <div class="media-body">
          <h4 class="media-heading">
            <?php
              $namapendek = explode(" ", Auth::user()->nama);
              echo $namapendek[0];
            ?>
          </h4>
          <span>{!! Auth::user()->job !!}</span>
        </div>
      </div>
      
      <ul class="nav nav-tabs nav-justified nav-sidebar">
        <li class="tooltips active" data-toggle="tooltip" title="Main Menu"><a data-toggle="tab" data-target="#mainmenu"><i class="tooltips fa fa-home"></i></a></li>
        <li class="tooltips" data-toggle="tooltip" title="Log Out"><a href="{{ url('/auth/logout') }}"><i class="fa fa-sign-out"></i></a></li>
      </ul>

      <div class="tab-content">

        <!-- ################# MAIN MENU ################### -->

        <div class="tab-pane active" id="mainmenu">
          <ul class="nav nav-pills nav-stacked nav-quirk">
            <li <?php if ($url == 'guru') { echo "class='active'"; } ?>><a href="{{ url('/guru') }}"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
          </ul>

          <ul class="nav nav-pills nav-stacked nav-quirk">
            <li class="nav-parent <?php if ($url == 'data-guru' or $url == 'detail-guru' or $url == 'kelas' or $url == 'detail-kelas' or $url == 'data-siswa' or $url == 'detail-kelas-siswa') { echo " active"; } ?>"><a href=""><i class="fa fa-database"></i> <span>Master Data</span></a>
              <ul class="children">
                <li <?php if ($url == 'data-guru' or $url == 'detail-guru') { echo "class='active'"; } ?>><a href="{{ url('/data-guru') }}"><i class="fa fa-user"></i> Guru</a></li>
                <li <?php if ($url == 'kelas' or $url == 'detail-kelas') { echo "class='active'"; } ?>><a href="{{ url('/kelas') }}"><i class="fa fa-building"></i> Kelas</a></li>
                <li <?php if ($url == 'data-siswa' or $url == 'detail-kelas-siswa') { echo "class='active'"; } ?>><a href="{{ url('/data-siswa') }}"><i class="fa fa-user"></i> Siswa</a></li>
              </ul>
            </li>

            <li class="nav-parent <?php if ($url == 'materi' or $url == 'soal-guru' or $url == 'detail-soal' or $url == 'ubah-detail-soal' or $url == 'edit-soal' or $url == 'detail-soal' or $url == 'hasil-guru' or $url == 'detail-hasil') { echo " active"; } ?>"><a href=""><i class="fa fa-graduation-cap"></i> <span>E-Learning</span></a>
              <ul class="children">
                <li <?php if ($url == 'materi') { echo "class='active'"; } ?>><a href="{{ url('/materi') }}">Materi</a></li>
                <li <?php if ($url == 'soal-guru' or $url == 'detail-soal' or $url == 'ubah-detail-soal') { echo "class='active'"; } ?>><a href="{{ url('/soal-guru') }}">Soal</a></li>
                <li <?php if ($url == 'hasil-guru' or $url == 'detail-hasil') { echo "class='active'"; } ?>><a href="{{ url('/hasil-guru') }}">Laporan</a></li>
              </ul>
            </li>

            <!-- <li class="nav-parent"><a href=""><i class="fa fa-th-list"></i> <span>Absensi</span></a>
              <ul class="children">
                <li <?php if ($url == 'input-absen') { echo "class='active'"; } ?>><a href="{{ url('/input-absen') }}">Input Absen</a></li>
                <li <?php if ($url == 'rekap-absen') { echo "class='active'"; } ?>><a href="{{ url('/rekap-absen') }}">Rekap Absen</a></li>
              </ul>
            </li> -->

            <li <?php if ($url == 'profil-guru') { echo "class='active'"; } ?>><a href="{{ url('/profil-guru') }}"><i class="fa fa-cog"></i> Pengaturan</a></li>
            
            
            <li><a href="{{ url('/auth/logout') }}"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div><!-- tab-pane -->
      </div><!-- tab-content -->
    </div><!-- leftpanelinner -->
  </div>
  <div class="mainpanel">
    <div class="contentpanel">
      <div class="row">
        <div class="col-md-12 dash-left">
          <div class="col-md-12 content">
            <ol class="breadcrumb">
              <li><a href="{{url('guru')}}">Home</a></li>
              <li><a href="{{ url('soal-guru') }}">Soal</a></li>
              <li class="active">{{ $soal->paket }}</li>
            </ol>
            <div class="panel panel-default">
              <div class="panel-heading" style="background: #072047; color: #fff">Detail Paket Soal</div>
              <div class="panel-body"> <a href="#" id="btsoal">
                <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Tambah soal">Tambah Soal</button>
                </a>
                <?php if ($soal->jenis == 1) { ?>
                  <a href="#" id="btkelas">
                  <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Lihat Kelas">Lihat Kelas</button>
                  </a>
                  <div id="wrapdistribusisoal" style="margin:15px 0 0 0; display:none;">
                    <div class="panel panel-default">
                      <div class="panel-body">
                        <h3 class="text-center">Daftar Kelas</h3>
                        <div class="alert alert-info" role="alert"><b>Perhatian!</b> Dibawah ini adalah daftar seluruh kelas dari <b>{{$school->nama}}</b>, Pilihlah kelas mana yang dapat melihat soal Anda dengan cara klik checkbox disisi kiri nama kelas. Tampilkan soal saat menjelang ujian dan sembunyikan soal saat sudah selesai diujikan.</div>
                        <div class="well">
                        @if($kelas->count())
                        @foreach($kelas as $data)
                          <?php
                            $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
                            if ($conn->connect_error) {
                              die("Connection failed: " . $conn->connect_error);
                            }
                            $sql = "SELECT * FROM distribusisoals WHERE id_soal = '$soal->id' AND id_kelas = '$data->id'";
                            $result = $conn->query($sql);
                          ?>
                          <input type="hidden" name="id_soal{{$data->id}}" id="id_soal{{$data->id}}" value="{{$soal->id}}">
                          <input type="hidden" name="id_kelassimpan{{$data->id}}" id="id_kelassimpan{{$data->id}}" value="{{$data->id}}">
                          <input type="checkbox" name="id_kelas{{$data->id}}" id="id_kelas{{$data->id}}" value="{{$data->id}}"
                          <?php
                            if ($result->num_rows > 0) {
                              while($row = $result->fetch_assoc()) {
                                echo "checked";
                              }
                            }
                          ?>
                          > {{ $data->nama }}<br>
                          <script>
                            $(document).ready(function() {
                              $("#id_kelas{{$data->id}}").change(function() {
                                if(this.checked) {
                                  var id_kelas = $("#id_kelassimpan{{$data->id}}").val();
                                  var id_soal = $("#id_soal{{$data->id}}").val();
                                  var datastring = "id_kelas="+id_kelas+"&id_soal="+id_soal;
                                  $.ajax({
                                    type: "POST",
                                    url: "{{ url('/simpandistribusikelas') }}",
                                    data: datastring,
                                  });
                                }else{
                                  var id_kelas = $("#id_kelassimpan{{$data->id}}").val();
                                  var id_soal = $("#id_soal{{$data->id}}").val();
                                  var datastring = "id_kelas="+id_kelas+"&id_soal="+id_soal;
                                  $.ajax({
                                    type: "POST",
                                    url: "{{ url('/hapusdistribusikelas') }}",
                                    data: datastring,
                                  });
                                }
                              });
                            });
                          </script>
                        @endforeach
                        @endif
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <div id="wrapsoal" style="margin:15px 0 0 0; display:none;">
                  <div class="well" style="background:#fff;">
                    <?php $sesi = md5(date('Y:m:d:H:s:i')) ?>
                    <div class="form-horizontal">
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Soal</label>
                        <div class="col-sm-10">
                          <input type="hidden" name="paket" id="paket" value="{{ $soal->id }}">
                          <textarea class="form-control" name="soal" id="soal" placeholder="Soal"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <label for="inputEmail3" class="col-sm-2 control-label">File Audio</label>
                        <div class="col-sm-10">
                          <form action="{{ url('/upload_file_audio')}}" method="post" class="dropzone">
                            <div class="fallback">
                              <input name="file" type="file" multiple />
                            </div>
                            <input type="hidden" name="tampil" id="tampil" value="N">
                            <input type="hidden" name="sesi" id="sesi" value="{{$sesi}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          </form>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pilihan A</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="pila" id="pila" placeholder="Pilihan A"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pilihan B</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="pilb" id="pilb" placeholder="Pilihan B"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pilihan C</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="pilc" id="pilc" placeholder="Pilihan C"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pilihan D</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="pild" id="pild" placeholder="Pilihan D"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pilihan E</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="pile" id="pile" placeholder="Pilihan E"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Kunci</label>
                        <div class="col-sm-10">
                          <select name="kunci" id="kunci" class="form-control" style="width: 50%;">
                            <option value="">-- Pilih Kunci Jawaban --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Score</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="score" id="score" placeholder="Score">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                          <select name="status" id="status" class="form-control" style="width: 50%;">
                            <option value="">-- Pilih Status Soal --</option>
                            <option value="Y">Tampil</option>
                            <option value="N">Tidak Tampil</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button type="submit" class="btn btn-primary" id="btnsimpansoal">Simpan</button>
                          <img src="{{ url('img/ajax-loader.gif') }}" alt="Loading" id="loading"> </div>
                      </div>
                      <div class="alert alert-danger" id="salah"></div>
                      <div class="alert alert-info" id="benar"><b>Sukses </b>Soal berhasil di buat.</div>
                    </div>
                  </div>
                </div>
                <hr class="clearfix">
                <table class="table table-responsive table-condensed table-hover table-bordered">
                  <caption>
                  Daftar soal untuk paket soal <b>{!! $soal->paket !!}</b>
                  </caption>
                  <thead>
                    <tr>
                      <th>NO</th>
                      <th>Soal</th>
                      <th>Kunci</th>
                      <th style="text-align:center;">Score</th>
                      <th style="text-align:center;">Status</th>
                      <th style="text-align:center;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $no=1; ?>
                  @if($detailsoals->count())
                  @foreach($detailsoals as $detailsoal)
                  <input type="hidden" name="id_soal{{ $detailsoal->id }}" id="id_soal{{ $detailsoal->id }}" value="{{ $detailsoal->id }}">
                  <tr>
                    <td>{{ $no++ }}</td>
                    <td>{!! $detailsoal->soal !!}</td>
                    <td align="center">{!! $detailsoal->kunci !!}</td>
                    <td align="center">{!! $detailsoal->score !!}</td>
                    <td align="center" valign="midle"><?php
                      if($detailsoal->status == "Y"){echo "<span style='background:#008000; color:#fff; padding:5px'>Tampil</span>";}else{echo "<span style='background:#cc0000; color:#fff; padding:5px'>Tidak</span>";}
                    ?></td>
                    <td width="120px" align="center"><a href="{{url('ubah-detail-soal', $detailsoal->id)}}" title="">Ubah</a> | <a href="#" id="hapussoal{{ $detailsoal->id }}" data-toggle="tooltip" title="Data soal yang dihapus tidak bisa dikembalikan.">Hapus</a></td>
                  </tr>
                  <tr class="alert alert-danger" style="display: none;" id="wrapth{{ $detailsoal->id }}">
                    <td colspan="6" id="tampilhapus{{ $detailsoal->id }}"></td>
                  </tr>
                  <script>
                    $(document).ready(function() {
                      $("#tampilhapus{{ $detailsoal->id }}").hide();
                      $('#hapussoal{{ $detailsoal->id }}').click(function () {
                        if (!confirm('Are you sure?')) return false;
                          var id_soal = $('#id_soal{{ $detailsoal->id }}').val();
                          var datastring = "id_soal="+id_soal;
                        $.ajax({
                          type: "POST",
                          url: "{{ url('hapusdetailsoal') }}",
                          data: datastring,
                          success: function(data){
                            $("#wrapth{{ $detailsoal->id }}").show();
                            $("#tampilhapus{{ $detailsoal->id }}").html(data).show();
                            location.reload();
                          }
                        });
                        return false;
                      });
                    });
                  </script> 
                  @endforeach
                  @else
                  <tr><td colspan="6" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
                  @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="rows">
        <div class="col-md-12">
          <div class="panel panel-primary">
            <div class="panel-body">
              Copyright &COPY; 2016 - {{ date('Y') }} <a href="http://www.tipa.co.id" target="blank">Tipamedia</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{url('lib/select2/select2.js')}}"></script> 
<script src="{{url('lib/summernote/summernote.js')}}"></script> 
<script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
<script src="{{url('lib/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{url('lib/bootstrap/js/bootstrap.js')}}"></script>
<script src="{{url('lib/jquery-toggles/toggles.js')}}"></script>
<script src="{{url('lib/morrisjs/morris.js')}}"></script>
<script src="{{url('lib/raphael/raphael.js')}}"></script>
<script src="{{url('lib/flot/jquery.flot.js')}}"></script>
<script src="{{url('lib/flot/jquery.flot.resize.js')}}"></script>
<script src="{{url('lib/flot-spline/jquery.flot.spline.js')}}"></script>
<script src="{{url('lib/jquery-knob/jquery.knob.js')}}"></script>
<script src="{{url('js/quirk.js')}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.backstretch("{{ url('/img/bg2.jpg') }}", {speed: 150});
  $(document).ready(function() {

    $("#btsoal").click(function() {
      $("#wrapsoal").toggle();
      return false;
    });

    $("#btkelas").click(function(){
    $("#wrapdistribusisoal").toggle();
    return false;
    });

      $("#loading").hide();
      $("#salah").hide();
      $("#benar").hide();
      $('.collapse').collapse();
      $('#kunci').select2();
      $('#status').select2();

        $("#soal").summernote({ height: 150 });
        $("#pila").summernote({ height: 150 });
        $("#pilb").summernote({ height: 150 });
        $("#pilc").summernote({ height: 150 });
        $("#pild").summernote({ height: 150 });
        $("#pile").summernote({ height: 150 });

    $("#btnsimpansoal").click(function() {
      $(this).hide();
      $("#loading").show();
      var paket = $("#paket").val();
      var sesi = encodeURIComponent($("#sesi").val());
      var soal = encodeURIComponent($("#soal").code());
      var pila = encodeURIComponent($("#pila").code());
      var pilb = encodeURIComponent($("#pilb").code());
      var pilc = encodeURIComponent($("#pilc").code());
      var pild = encodeURIComponent($("#pild").code());
      var pile = encodeURIComponent($("#pile").code());
      var kunci = $("#kunci").val();
      var score = $("#score").val();
      var status = $("#status").val();
      var datastring = "paket="+paket+"&soal="+soal+"&pila="+pila+"&pilb="+pilb+"&pilc="+pilc+"&pild="+pild+"&pile="+pile+"&kunci="+kunci+"&score="+score+"&status="+status+"&sesi="+sesi;
      $.ajax({
        type: "POST",
        url: "{{ url('/simpanformdetailsoal') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#salah").hide();
            $("#kunci").val("");
            $("#score").val("");
            $("#status").val("");
            $("#paket").val("");
            $("#soal").code("");
            $("#pila").code("");
            $("#pilb").code("");
            $("#pilc").code("");
            $("#pild").code("");
            $("#pile").code("")
            $("#benar").show();
            $("#btnsimpansoal").show();
            location.reload();
          }else{
            $("#loading").hide();
            $("#benar").hide();
            $("#salah").html(data).show();
            $("#btnsimpansoal").show();
          }
        }
      });
      return false;
    });
    });
</script>
<?php
  }else{
    return redirect('url(siswa)');
  }
?>
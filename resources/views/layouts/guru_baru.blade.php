<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>@yield('title')</title>
<link rel="stylesheet" href="{{ url('lib/fontawesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ url('css/admin.css') }}">
<link rel="icon" href="{{ url('img/favicon.png') }}">
<script src="{{url('js/modernizr.js')}}"></script>
<link rel="stylesheet" href="{{ url('lib/Hover/hover.css') }}">
<link rel="stylesheet" href="{{ url('lib/weather-icons/css/weather-icons.css') }}">
<link rel="stylesheet" href="{{ url('lib/jquery-toggles/toggles-full.css') }}">
<link rel="stylesheet" href="{{ url('lib/morrisjs/morris.css') }}">
</head>
<body>
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
  include(app_path() . '/functions/koneksi.php');
  if (Auth::user()->status != "S" or Auth::user()->status != "C") {
  $id_kelas = $user->id_kelas;

  $sql = "SELECT * FROM kelas WHERE id = '$id_kelas'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
  	while($row = $result->fetch_assoc()) {
  		$kelas_siswa = $row["nama"];
  	}
  } else {
	$kelas_siswa = "Maaf, Anda belum mendapat kelas";
  }
  $tgl_log_user = explode(" ", Auth::user()->updated_at);
  $bulan_log_user = explode("-", $tgl_log_user[0]);
  $url = Request::segment(1);
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
        @yield('content')
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-primary">
            <div class="panel-body">
              Copyright &COPY; 2016 - {{ date('Y') }} <a href="http://www.tipa.co.id" target="blank">Tipamedia</a>
              <span class="pull-right">versi 1.1</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- </div> -->
<?php
  }else{
	  return redirect('url(siswa)');
  }
?>
</section>
<!-- <script src="{{url('lib/jquery/jquery.js')}}"></script> -->
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{ url('lib/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ url('lib/bootstrap/js/bootstrap.js') }}"></script>
<script src="{{ url('lib/jquery-toggles/toggles.js') }}"></script>

<script src="{{ url('js/quirk.js') }}"></script>
<script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.backstretch("{{ url('/img/bg2.jpg') }}", {speed: 150});
</script>
</body>
</html>
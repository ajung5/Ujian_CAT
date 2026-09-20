<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Hapus Paket Soal</title>
<link rel="stylesheet" href="{{ url('lib/fontawesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ url('css/admin.css') }}">
<link rel="icon" href="{{ ('img/favicon.png') }}">
<script src="{{url('js/modernizr.js')}}"></script>
<link rel="stylesheet" href="{{url('lib/Hover/hover.css')}}">
<link rel="stylesheet" href="{{url('lib/weather-icons/css/weather-icons.css')}}">
<link rel="stylesheet" href="{{url('lib/jquery-toggles/toggles-full.css')}}">
<link rel="stylesheet" href="{{url('lib/morrisjs/morris.css')}}">
</head>
<body>
<?php
	$id = Request::segment(2);
?>
<section>
  <div>
  	<div class="mainpanel">
    <div class="contentpanel">
      <div class="row">
        <div class="col-md-12 dash-left">
        	<div class="alert alert-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Yakin Data Paket soal ini akan dihapus? Data yang dihapus tidak bisa dikembalikan. Data yang dihapus meliputi detail soal dalam paket yang dipilih. <a href="{{ url('eksekusi-hapus-paket-soal/'.$id) }}" style="text-decoration: none;"><button type="button" class="btn btn-success btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i> Hapus</button></a> <a href="javascript:close_window();" style="text-decoration: none;"><button type="button" class="btn btn-primary btn-sm"><i class="fa fa-ban" aria-hidden="true"></i> Batal</button></a> ?</div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script type="text/javascript">
	function close_window() {
	  close();
	}
</script>
</body>
</html>
@extends('layouts/guru_baru')
@section('title', $materi->judul)
@section('content')
<link href="{{ url('/lib/dropzone/dropzone.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{url('lib/summernote/summernote.css')}}">
<link rel="stylesheet" href="{{url('lib/fa/css/font-awesome.min.css')}}">
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
    <li><a href="{{ url('/materi') }}">Materi</a></li>
    <li class="active">Detail: {{ $materi->judul }}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Lengkapi form dibawah ini.</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
        	<h3>{{ $materi->judul }}</h3>
        	<p>
        		<?php if ($materi->gambar != "") { ?>
          		<img src="{{ url('/img/materi/'.$materi->gambar) }}" class="img img-thumbnail" alt="img">
          	<?php } ?>
        	</p>
        	<p>
        		<?php
        			$tanggal = explode(" ", $materi->created_at);
        			$tanggal = explode("-", $tanggal[0]);
        			$tanggal = $tanggal[2].' '.$bulan[$tanggal[1]].' '.$tanggal[0];
        		?>
        		<i class="fa fa-calendar" aria-hidden="true"></i> {{ $tanggal }}
        	</p>
        	<p>
        		{!! $materi->isi !!}
        	</p>
          <hr>
          <a href="{{ url('/materi/ubah/'.$materi->id) }}" data-toggle='tooltip' title="Ubah materi" class="btn btn-primary btn-md"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah Materi</a>
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
<script src="{{ url('/lib/dropzone/dropzone.js') }}"></script>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{url('lib/summernote/summernote.js')}}"></script>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  jQuery.noConflict();
    (function ($) {
      function readyFn() {
        $("#isi").summernote({ height: 150 });

        $("#btn-materi").click(function(){
		    	$("#wrap-materi").slideToggle();
		    });
		    $("#batal").click(function(){
		    	$("#wrap-materi").slideToggle();
		    });

		    
		    $("#simpan").click(function(){
		    	$("#loading").show();
		    	var sesi = $("#sesi").val();
		    	var judul = $("#judul").val();
		    	var isi = encodeURIComponent($("#isi").code());
		    	var status = $("input[name=status]:checked").val();
		    	var dataString = 'sesi='+sesi+'&judul='+judul+'&isi='+isi+'&status='+status;
		    	$.ajax({
		    		type: "POST",
		    		url: "{{ url('/simpan-materi') }}",
		    		data: dataString,
		    		success: function(data){
		    			if (data == 'ok') {
		    				$("#loading").hide();
		    				$("#notif").removeClass('alert alert-danger').addClass('alert alert-info').html('Materi berhasil disimpan.').fadeIn(350);
		    				window.location.href = "{{ url('/materi') }}"
		    			}else{
		    				$("#loading").hide();
		    				$("#notif").removeClass('alert alert-info').addClass('alert alert-danger').html(data).fadeIn(350);
		    			}
		    		}
		    	})
		    });
      }

      $(document).ready(readyFn); 
  })(jQuery);

</script>
@endsection
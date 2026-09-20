@extends('layouts/guru_baru')
@section('title', 'Ubah materi')
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
    <li class="active">Ubah: {{ $materi->judul }}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Lengkapi form dibawah ini.</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
        	<div class="wells" style="margin: 15px 0 0 0; background: #fff" id="wrap-materi">
        		<div class="form-horizontal" style="margin: 0; padding-bottom: 0 !important;">
        			<div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Judul</label>
	              <div class="col-sm-10">
	              	{{ csrf_field() }}
	                <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul" value="{{ $materi->judul }}">
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Isi</label>
	              <div class="col-sm-10">
	                <textarea class="form-control" name="isi" id="isi" placeholder="Isi" style="background: #fff">{{ $materi->isi }}</textarea>
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Gambar</label>
	              <div class="col-sm-10">
	              	<?php if ($materi->gambar != "") { ?>
	              		<img src="{{ url('/img/materi/'.$materi->gambar) }}" class="img img-thumbnail" style="width: 250px; margin-bottom: 15px" alt="img">
	              	<?php } ?>
	                <form action="{{ url('/upload-gambar-materi') }}" class="dropzone">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="id" name="id" value="{{ $user->id }}">
                    <input type="hidden" id="sesi" name="sesi" value="{{ $materi->sesi }}">
                    <div class="fallback">
                      <input name="file" type="file" multiple />
                    </div>
                  </form>
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
	              <div class="col-sm-10">
	                <input type="radio" name="status" value="N" style="margin-top: 12px" <?php if ($materi->status == 'N') { echo "checked"; } ?>> Tidak tampil&nbsp;&nbsp;&nbsp;&nbsp;
	                <input type="radio" name="status" value="Y" style="margin-top: 12px" <?php if ($materi->status == 'Y') { echo "checked"; } ?>> Tampil
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">&nbsp;</label>
	              <div class="col-sm-10">
	              	<img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading" style="display: none">
	              	<div id="notif" style="display: none"></div>
	                <input type="button" id="simpan" class="btn btn-success btn-sm" value="Simpan">
	                <input type="button" onclick="self.history.back();" class="btn btn-danger btn-sm" value="Batal">
	              </div>
	            </div>
        		</div>
        	</div>
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
<script src="{{ url('lib/bootstrap/js/bootstrap.js') }}"></script>
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
		    	var judul = encodeURIComponent($("#judul").val());
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
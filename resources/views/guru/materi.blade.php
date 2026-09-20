@extends('layouts/guru_baru')
@section('title', 'Materi')
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
    <li class="active">Anda berada di halaman depan</li>
  </ol>
  <div class="panel panel-announcement">
    <ul class="panel-options">
      <li><a class="panel-remove"><i class="fa fa-remove"></i></a></li>
    </ul>
    <div class="panel-body">
      <h2>Materi.</h2>
      <h4>Anda dapat menulis sebuah materi yang dapat diakses oleh seluruh siswa yang memiliki akses untuk menggunakan aplikasi ini. Materi juga bisa disertai dengan soal-soal latihan.</h4>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Data Materi Anda.</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
        	<button type="button" class="btn btn-primary btn-md" id="btn-materi">Tulis Materi</button>
        	<div class="wells" style="margin: 15px 0 0 0; display: none; background: #fff" id="wrap-materi">
        		<div class="form-horizontal" style="margin: 0; padding-bottom: 0 !important;">
        			<div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Judul</label>
	              <div class="col-sm-10">
	              	{{ csrf_field() }}
	                <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul">
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Isi</label>
	              <div class="col-sm-10">
	                <textarea class="form-control" name="isi" id="isi" placeholder="Isi" style="background: #fff"></textarea>
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Gambar</label>
	              <div class="col-sm-10">
	                <form action="{{ url('/upload-gambar-materi') }}" class="dropzone">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="id" name="id" value="{{ $user->id }}">
                    <input type="hidden" id="sesi" name="sesi" value="{{ md5(Auth::user()->id.rand(000000, 999999)) }}">
                    <div class="fallback">
                      <input name="file" type="file" multiple />
                    </div>
                  </form>
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
	              <div class="col-sm-10">
	                <input type="radio" name="status" value="N" style="margin-top: 12px" checked> Tidak tampil&nbsp;&nbsp;&nbsp;&nbsp;
	                <input type="radio" name="status" value="Y" style="margin-top: 12px"> Tampil
	              </div>
	            </div>
	            <div class="form-group">
	              <label for="inputEmail3" class="col-sm-2 control-label">&nbsp;</label>
	              <div class="col-sm-10">
	              	<img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading" style="display: none">
	              	<div id="notif" style="display: none"></div>
	                <input type="button" id="simpan" class="btn btn-success btn-sm" value="Simpan">
	                <input type="button" id="batal" class="btn btn-danger btn-sm" value="Batal">
	              </div>
	            </div>
        		</div>
        	</div>
        	<div class="form-horizontal" style="margin: 10px 0 5px 0">
		        <input type="text" class="form-control" id="q" placeholder="Cari berdasarkan Nama (Ketik lalu enter)">
		      </div>
		      <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading" id="loading_cari" style="display: none;">
        	<div class="table-responsive" id="wrap-table-materi" style="margin-top: 15px">
	          <table class="table">
	          	<thead>
	          		<tr>
	          			<th>#</th>
	          			<th>Judul</th>
	          			<th>Status</th>
	          			<th style="text-align: center">Aksi</th>
	          		</tr>
	          	</thead>
	          	<tbody>
	          		<?php $no = $materis->firstItem(); ?>
	          		@if($materis->count())
	          		@foreach($materis as $materi)
	          		<?php
	          			if ($materi->status == 'Y') {
	          				$status = "<span class='label label-primary'>Tampil</span>";
	          			}else{
	          				$status = "<span class='label label-danger'>Tidak tampil</span>";
	          			}
	          		?>
	          		<tr id="baris{{ $materi->id }}">
	          			<td style="width: 30px">{{ $no++ }}</td>
	          			<td>{{ $materi->judul }}</td>
	          			<td style="width: 75px">{!! $status !!}</td>
	          			<td style="text-align: center; width: 120px">
	          				<a href="{{ url('/materi/ubah/'.$materi->id) }}" data-toggle='tooltip' title="Ubah materi" class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
	          				<button data-toggle='tooltip' title="Hapus materi" class="btn btn-danger btn-xs" id="hapus{{ $materi->id }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
	          				<a href="{{ url('/materi/detail/'.$materi->id) }}" data-toggle='tooltip' title="Detail materi" class="btn btn-success btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>
	          			</td>
	          			<input type="hidden" name="id" id="id{{ $materi->id }}" value="{{ $materi->id }}">
	          			<script>
		                $(document).ready(function(){
		                	$("#hapus{{ $materi->id }}").click(function(){
		                    if (confirm('Yakin data akan dihapus?')) {
		                    	var id = $("#id{{ $materi->id }}").val();
		                    	$.ajax({
		                        type: "POST",
		                        url: "{{ url('/hapus_materi') }}",
		                        data: 'id='+id,
		                        success: function(data){
		                          if(data == "berhasil"){
		                            $("#baris{{ $materi->id }}").hide();
		                          }else{
		                            alert('Gagal menghapus data');
		                          }
		                        }
		                      });
		                    }
		                  });
		                });
		              </script>
	          		</tr>
	          		@endforeach
	          		@else
	          		<tr><td colspan="4" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
	          		@endif
	          	</tbody>
	          </table>
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
<script src="{{ url('lib/bootstrap/js/bootstrap.js') }}"></script>
<script src="{{ url('/lib/dropzone/dropzone.js') }}"></script>
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

		    $("#q").keyup(function(e){
		      if(e.keyCode == 13)
		      {
		      	$("#wrap-table-materi").hide();
		        $("#loading_cari").show();
		        var q = encodeURIComponent($("#q").val());
		        $.ajax({
		          type: "POST",
		          url: "{{ url('/get-materi') }}",
		          data: 'q='+q,
		          success: function(data){
		            $("#loading_cari").hide();
		            $("#wrap-table-materi").hide().html(data).fadeIn(350);
		          }
		        })
		      }
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
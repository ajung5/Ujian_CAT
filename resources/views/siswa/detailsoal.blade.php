<?php
	header('Content-Type: text/html; charset=utf-8');
	if (Auth::user()->status == "S" or Auth::user()->status == "C") {

	include(app_path() . '/functions/koneksi.php');

	$id_kelas = $user->id_kelas;
	$conn = new mysqli($hostdb, $userdb, $passdb, $namedb);

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	$sql = "SELECT * FROM kelas WHERE id = '$id_kelas'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	        $kelas_siswa = $row["nama"];
	    }
	} else {
	    $kelas_siswa = "Maaf, Anda belum mendapat kelas";
	}
	
	$q_cekdistribusisoal = "SELECT * FROM distribusisoals WHERE id_soal = '$idsoal' AND id_kelas = '$id_kelas'";
	$result = $conn->query($q_cekdistribusisoal);
	if ($result->num_rows > 0) {
	    $jumlah_kelas = $result->num_rows;
	} else {
	    header("Refresh: 0;../soal-siswa");
	}

	$q_cekjenissoal = "SELECT * FROM detailsoals WHERE id_soal = '$idsoal' GROUP BY id_soal";
	$result = $conn->query($q_cekjenissoal);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	        $jenis_soal = $row["jenis"];
	    }
	} else {
	    $jenis_soal = "";
	}

	$q_cekjawabsoal = "SELECT * FROM jawabs WHERE id_soal = '$idsoal' AND id_user = '$user->id' AND status = 'Y'";
	$result = $conn->query($q_cekjawabsoal);
	if ($result->num_rows > 0) {
	    
	    if ($jenis_soal == 1 AND $result->num_rows >= 1) {
	    	$sudahmengerjakan = 1;
	    }else{
	    	$sudahmengerjakan = 0;
	    }
	    
	}else{
		$sudahmengerjakan = 0;
	}

	$sql_soal = "SELECT * FROM detailsoals WHERE id_soal = '$idsoal' AND status='Y' ORDER BY RAND()";
	
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Detail Soal - Aplikasi Ujian Berbasis Komputer</title>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="icon" href="{{ url('img/favicon.png') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="{!! url('css/anythingslider.css') !!}">
<link rel="stylesheet" href="{!! url('css/theme-minimalist-round.css') !!}">
<link rel="stylesheet" href="{!! url('css/flipclock.css') !!}">
<style>
.wrapsoal {
	min-height: 150px;
	padding: 15px 100px 75px 15px;
	line-height: 25px;
}
#soal {
	padding: 0 0 15px 0;
}
</style>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
<script src="{!! url('js/jquery.anythingslider.js') !!}"></script>
<script src="{!! url('js/flipclock.js') !!}"></script>

<div class="container" style="margin: 15px auto 0 auto">
  <div class="col-md-12 content">
    <ol class="breadcrumb">
      <li><a href="{!! url('/siswa') !!}">Home</a></li>
      <li><a href="{!! url('/soal-siswa') !!}">Soal</a></li>
      <li class="active">Detail Soal</li>
    </ol>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Detail Soal</h3>
      </div>
      <div class="panel-body">
        <table class="table">
          <tbody>
          
          @if($distribusisoal->count())
          @foreach($distribusisoal as $distribusisoal)
          <?php
				$conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
				if ($conn->connect_error) {
				    die("Connection failed: " . $conn->connect_error);
				}
				$sql = "SELECT * FROM soals WHERE id = '$idsoal' LIMIT 1";
			?>
          @endforeach
          @endif
          <?php
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
				    while($row = $result->fetch_assoc()) {
				    	$id_guru = $row['id_user'];
				    	$id_soal = $row['id'];
				    	$paket_soal = $row['paket'];
				    	$waktu_soal = $row['waktu'];
				    	$deksripsi_soal = $row['deskripsi'];
				    	$kkm_soal = $row['kkm'];
			?>
          <tr>
            <td width="150px">Test</td>
            <td>: {{ $paket_soal }}</td>
          </tr>
          <tr>
            <td>Jumlah Soal</td>
            <td>:
              <?php
						$conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
						if ($conn->connect_error) {
						    die("Connection failed: " . $conn->connect_error);
						}
						$sqljs = "SELECT * FROM detailsoals WHERE id_soal = '$idsoal' AND status = 'Y'";
						$resultjs = $conn->query($sqljs);
						if ($resultjs->num_rows > 0) {
						    echo $resultjs->num_rows;
						} else {
						    echo "0";
						}
					?></td>
          </tr>
          <tr>
            <td>KKM</td>
            <td>: {{ $kkm_soal }}</td>
          </tr>
          <tr>
            <td>Waktu</td>
            <td>:
              <?=  $waktu_soal / 60; ?>
              menit</td>
          </tr>
          <tr>
            <td>Deskripsi</td>
            <td>: {!! $deksripsi_soal !!}</td>
          </tr>
          <?php }} ?>
            </tbody>
          
        </table>
        <div class="alert alert-success">
          <p style="font-size:18px; margin-bottom:10px;">Sebelum mulai mengerjakan, baca dan ikuti instruksi dibawah ini:</p>
          <ul>
            <li><b style="color: #F00;">Jangan</b> <i>refresh</i> halaman, atau jawaban akan hilang.</li>
            <li>Selalu perhatikan waktu ujian, karena sistem akan mengumpulkan jawaban secara otomatis saat waktu ujian telah habis.</li>
          </ul>
        </div>
        <?php if ($sudahmengerjakan == 0) { ?>
        <button type="button" id="btnmulai" class="btn btn-primary btn-lg">Mulai Ujian</button>
        <?php }else{ ?>
        <div class="alert alert-danger" style="font-size:20px; font-weight:bold"><span class="glyphicon glyphicon-remove-sign"></span> Anda sudah mengerjakan soal ini......</div>
        <?php	} ?>
      </div>
    </div>
    <!-- </div> -->
    <div id="soal" class="container" style="background: #fff; overflow-y: scroll;">
      <div class="col-md-12">
        <div class="alert alert-danger" style="font-size: 14px; font-weight: bold;" id="wrapwaktu"><span class="glyphicon glyphicon-time"></span> <span id="waktuujian"></span></div>
        <div class="message"></div>
        <br class="clearfix">
        <ul id="slider" class="wrapslidersoal">
		<?php 
		$conn->set_charset("utf8");
		$result = $conn->query($sql_soal);
		$no = 1;
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		?>
          <li>
            <div class="wrapsoal">
              <input type="hidden" name="id_soaljawab" id="id_soaljawab" value="{{ $row['id_soal'] }}">
              <input type="hidden" name="id_userjawab" id="id_userjawab" value="{{ Auth::user()->id }}">
              <input type="hidden" name="id_soal{{ $row['id'] }}" id="id_soal{{ $row['id'] }}" value="{{ $row['id_soal'] }}">
              <input type="hidden" name="no_soal_id{{ $row['id'] }}" id="no_soal_id{{ $row['id'] }}" value="{{ $row['id'] }}">
              <input type="hidden" name="id_user{{ $row['id'] }}" id="id_user{{ $row['id'] }}" value="{{ Auth::user()->id }}" >
              <table>
                <tbody>
                  <tr>
                    <td valign="top" width="25px"><b>{{ $no++ }}.</b></td>
                    <td colspan="2">
                    	<?php $datasoal = $row['soal']; ?>
                    	
                    	{!! $datasoal !!}
                    	<?php if($row['audio'] != ""){ $audio = $row['audio']; ?>
                    	<p>
                    		<audio controls>
			                  <source src="{{ url('/assets/audios/'.$audio) }}" type="audio/mpeg">
			                  Your browser does not support the audio element.
			                </audio>
                    	</p>
                    	<?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" height="15px"></td>
                  </tr>
                  <tr id="pila{{ $row['id'] }}">
                    <td valign="top" width="25px">&nbsp;</td>
                    <td valign="top" width="25px"><input type="radio" name="pil{{ $row['id'] }}" id="pil{{ $row['id'] }}" value="A"></td>
                    <td>{!! $row['pila'] !!}</td>
                  </tr>
                  <tr>
                    <td colspan="3" height="10px"></td>
                  </tr>
                  <tr id="pilb{{ $row['id'] }}">
                    <td valign="top" width="25px">&nbsp;</td>
                    <td valign="top" width="25px"><input type="radio" name="pil{{ $row['id'] }}" id="pil{{ $row['id'] }}" value="B"></td>
                    <td>{!! $row['pilb'] !!}</td>
                  </tr>
                  <tr>
                    <td colspan="3" height="10px"></td>
                  </tr>
                  <tr id="pilc{{ $row['id'] }}">
                    <td valign="top" width="25px">&nbsp;</td>
                    <td valign="top" width="25px"><input type="radio" name="pil{{ $row['id'] }}" id="pil{{ $row['id'] }}" value="C"></td>
                    <td>{!! $row['pilc'] !!}</td>
                  </tr>
                  <tr>
                    <td colspan="3" height="10px"></td>
                  </tr>
                  <tr id="pild{{ $row['id'] }}">
                    <td valign="top" width="25px">&nbsp;</td>
                    <td valign="top" width="25px"><input type="radio" name="pil{{ $row['id'] }}" id="pil{{ $row['id'] }}" value="D"></td>
                    <td>{!! $row['pild'] !!}</td>
                  </tr>
                  <tr>
                    <td colspan="3" height="10px"></td>
                  </tr>
                  <tr id="pile{{ $row['id'] }}">
                    <td valign="top" width="25px">&nbsp;</td>
                    <td valign="top" width="25px"><input type="radio" name="pil{{ $row['id'] }}" id="pil{{ $row['id'] }}" value="E"></td>
                    <td>{!! $row['pile'] !!}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <script type="text/javascript">
				$.ajaxSetup({
			      headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			      }
			    });
				$(document).ready(function(){
					$("input[name=pil{{ $row['id'] }}]").click(function(){
						var pilihan = $("input[name=pil{{ $row['id'] }}]:checked").val();
						var id_soal = $("#id_soal{{ $row['id'] }}").val();
						var no_soal_id = $("#no_soal_id{{ $row['id'] }}").val();
						var id_user = $("#id_user{{ $row['id'] }}").val();
						var datastring = "pilihan="+pilihan+"&id_soal="+id_soal+"&no_soal_id="+no_soal_id+"&id_user="+id_user;
				        $.ajax({
				          type: "POST",
				          url: "{!! url('simpanjawabankliksiswa') !!}",
				          data: datastring,
				          success: function(data){
				          	if(data == "A"){
				          		$("#pilb{{ $row['id'] }}").removeClass('alert alert-info');
				          		$("#pilc{{ $row['id'] }}").removeClass('alert alert-info');
				          		$("#pild{{ $row['id'] }}").removeClass('alert alert-info');
				          		$("#pile{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pila{{ $row['id'] }}").addClass('alert alert-info');
				            }
				            if(data == "B"){
				            	$("#pilc{{ $row['id'] }}").removeClass('alert alert-info');
				          		$("#pild{{ $row['id'] }}").removeClass('alert alert-info');
				          		$("#pile{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pila{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilb{{ $row['id'] }}").addClass('alert alert-info');
				            }
				            if(data == "C"){
				            	$("#pild{{ $row['id'] }}").removeClass('alert alert-info');
				          		$("#pile{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pila{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilb{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilc{{ $row['id'] }}").addClass('alert alert-info');
				            }
				            if(data == "D"){
				            	$("#pile{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pila{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilb{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilc{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pild{{ $row['id'] }}").addClass('alert alert-info');
				            }
				            if(data == "E"){
				            	$("#pila{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilb{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pilc{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pild{{ $row['id'] }}").removeClass('alert alert-info');
				            	$("#pile{{ $row['id'] }}").addClass('alert alert-info');
				            }
				            console(data);
				          }
				        });
						//alert(pilihan);
					});
				});
			</script> 
          </li>
          <?php
		}
	} else {
	    echo "Maaf, Soal belum dibuat...";
	}
	$conn->close();
	?>
        </ul>
        <center>
          <input type="button" name="kirimjawaban" id="btnjawab" value="Kirim Jawaban" class="btn btn-primary pull-right">
          <div class="clearfix"></div>
          <div style="height: 10px;"></div>
          <div class="alert alert-success" id="jawabbagus" style="font-size:20px; text-align:center">Ujian telah telah selesai dikerjakan dan dilakukan dengan baik.</div>
          <div class="alert alert-danger" id="jawabjelek" style="font-size:20px; text-align:center">Ujian telah telah selesai dikerjakan dan dilakukan dengan baik.</div>
        </center>
      </div>
    </div>
    <footer class="col-md-12 pull-left footer" style="background:#fff">
      <p class="col-md-12">
      <hr class="divider">
      Copyright &COPY; 2016 {{ $school->nama }} Design by: <a href="http://www.tipa.co.id" target="_blank">Tipamedia</a>
      </p>
    </footer>
  </div>
  <script>
	$.backstretch("{{ url('/img/bg_ujian.jpg') }}", {speed: 150});
	function disableF5(e) {
		if (e.which == 116) e.preventDefault();
	};
	$(document).bind("keydown", disableF5)
	function preventBack(){window.history.forward();}
	   	setTimeout("preventBack()", 0);
	   	window.onunload = function(){null};
	   	function Disable() {
		if (event.button == 2){
			alert("klik kanan tidak diaktifkan.")
		}
	}
	document.onmousedown = Disable;

	var clock;		
	$(document).ready(function() {
		$("#jawabbagus").hide();
	  	$("#jawabjelek").hide();
	  	$("#btnjawab").click(function(){
	  		if (!confirm('Yakin jawaban akan dikirim?')) return false;
		    
		    var id_soaljawab = $("#id_soaljawab").val();
		    var id_userjawab = $("#id_userjawab").val();
		    var datastring = "id_soaljawab="+id_soaljawab+"&id_userjawab="+id_userjawab;
		    $.ajax({
		    	url: "{{ url('/kirimjawaban') }}",
		    	type: 'POST',
		    	data: datastring,
		    	success: function(data){
		    		$("#btnjawab").hide();
	    			$("#jawabjelek").hide();
	    			$("#jawabbagus").show();
	    			setTimeout(function() {
						    window.location.href = "{{ url('/siswa') }}"
							}, 15);
		    		/*if(data >= {{ $kkm_soal }}){
		    			$("#btnjawab").hide();
	  					$("#jawabjelek").hide();
		    			$("#jawabbagus").show();
		    			setTimeout(function() {
						    window.location.href = "../hasil-siswa"
							}, 8);
		    		}else if(data < $kkm_soal){
		    			$("#btnjawab").hide();
		    			$("#jawabbagus").hide();
		    			$("#jawabjelek").show();
		    			setTimeout(function() {
					    	window.location.href = "../hasil-siswa"
							}, 8);
		    		}else{
		    			$("#btnjawab").hide();
		    			$("#jawabjelek").hide();
		    			$("#jawabbagus").show();
		    		}*/
	        }
		    })
	  	});

			clock = $('#waktuujian').FlipClock({{ $waktu_soal }}, {
        clockFace: 'MinuteCounter',
        countdown: true,
        autoStart: false,
        callbacks: {
        	start: function() {
        		alert ('Ujian dimulai......');
        	},
        	stop: function() {
        		window.location.href = "../hasil-siswa";
        	}
        }
	    });

	    $('#soal').click(function(e) {
	    	clock.start();
	    });
		});
	
    var elem = document.getElementById("soal");
    var btnelem = document.getElementById("btnmulai");

    window.onload = function() {
    	if (!window.screenTop && !window.screenY) {
    		$('#slider').anythingSlider({
					easing : 'easeInOutBack',
					resizeContents : false,
					buildStartStop : false,
					onSlideBegin: function(e,slider) {
						slider.navWindow( slider.targetPage );
					}
				});
	    	$("#soal").hide();
	        console.log('not fullscreen');
	    } else {
	    	$('#slider').anythingSlider({
				easing : 'easeInOutBack',
				resizeContents : false,
				buildStartStop : false,
				onSlideBegin: function(e,slider) {
					slider.navWindow( slider.targetPage );
				}
			});
	    	$("#soal").hide();
	        console.log('fullscreen');
	    }
    }

    btnelem.onclick = function() {
    	$("#soal").show();
        req = elem.requestFullScreen || elem.webkitRequestFullScreen || elem.mozRequestFullScreen;
        req.call(elem);
    }
   
    $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function(e)    
		{
	    if (!window.screenTop && !window.screenY) {
	    	$("#soal").show();
	        console.log('not fullscreen');
	    } else {
	    	$("#soal").hide();
	        console.log('fullscreen');
	    }
	});

  </script>
</div>
<?php
	}else{
		return redirect('url(guru)');
	}
?>

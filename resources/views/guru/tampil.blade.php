<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Ujian</title>
    <link rel="icon" href="{{ ('img/favicon.png') }}">
    <link rel="stylesheet" href="{{ url('assets_lama/bootstrap/css/bootstrap.min.css') }}">
		<style type="text/css" media="screen">
			#container 
			{
		    overflow:hidden;
			}

			#fancy_h1_wrap
			{
		    display:block;
		    width:84%;
		    height:100%;
		    position:absolute;
		    top:100%;
			}
			body{
				background-color: #8F6253;
			}
		</style>
	</head>
	<body>
		<div id="container" class="container">
		  <div id="fancy_h1_wrap">
		  <table class="table table-bordered table-striped table-condensed table-responsive" style="background-color: #fff;">
		  	<tr>
		  		<th>ID Pendaftaran</th>
		  		<th>Nama</th>
		  		<th>Asal Sekolah</th>
		  		<th>Nilai</th>
		  	</tr>
			  <?php
			  	include(app_path() . '/functions/koneksi.php');
			  	$no = 1;
			  ?>
		    @if($jawabs->count())
		    @foreach($jawabs as $jawab)
			    <?php
			    	$id_user = $jawab->id_user;
			    	$conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
				    $conn->set_charset("utf8");
						if ($conn->connect_error) {
						  die("Connection failed: " . $conn->connect_error);
						}
						$sql = "SELECT sum(score) as total FROM jawabs WHERE id_kelas='$jawab->id_kelas' AND id_soal='$jawab->id_soal' AND id_user='$jawab->id_user'";
						$datatotal = $conn->query($sql);
						while ($row = mysqli_fetch_assoc($datatotal))
						{
						  $nilai = $row['total'];
						}
						$sql = "SELECT id, nama, no_induk, sekolah_asal FROM users WHERE id='$id_user'";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
				?>
						<tr>
							<td><b>{{ $row['no_induk'] }}</b></td>
							<td>{{ $row['nama'] }}</td>
							<td>{{ $row['sekolah_asal'] }}</td>
							<td><b><?= $nilai; ?></b></td>
						</tr>
				<?php }} ?>
				@endforeach
		    @endif
		    </table>
		  </div>
		</div>
		<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
		<script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
		<script>
			$.backstretch("{{ url('/img/bg_guru.jpg') }}", {speed: 150});
			function fun(){
		    $('#fancy_h1_wrap').css('top', '');
		    $('#fancy_h1_wrap').animate({top:"-100%"}, 25000, fun);

			}
			fun();
		</script>
	</body>
</html>
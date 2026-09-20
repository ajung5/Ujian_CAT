<style type="text/css" media="screen">
.table td, .table th {
    background-color: #fff;
 }
table {
  border-spacing: 0;
  border-collapse: collapse;
}
.table-bordered th,.table-bordered td {
    border: 1px solid #ddd;
 }
</style>
<?php include(app_path() . '/functions/koneksi.php');
	$id_kelas = Request::segment(2);
	$id_soal = Request::segment(3);
  /*$conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	}*/
	$sqltitle = "SELECT paket FROM soals WHERE id='$id_soal'";
	$result = $conn->query($sqltitle);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	    	$paket_soal = $row['paket'];
	    }
	}
	$sqltitle = "SELECT nama FROM kelas WHERE id='$id_kelas'";
	$result = $conn->query($sqltitle);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	    	$kelas = $row['nama'];
	    }
	}
?>

<table>
	<tbody>
		<tr>
			<td><b>Paket Soal</b></td>
			<td colspan="4">{{ $paket_soal }}</td>
		</tr>
		<tr>
			<td><b>Kelas</b></td>
			<td colspan="4">{{ $kelas }}</td>
		</tr>
	</tbody>
</table>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>NIS</th>
			<th>Nama</th>
			<th>Jumlah Soal</th>
			<th>Jawaban Benar</th>
			<th>Nilai</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$sql = "SELECT * FROM jawabs WHERE id_kelas='$id_kelas' AND id_soal='$id_soal' GROUP BY id_user ORDER BY nama ASC";
		$hasil = $conn->query($sql);
		if ($hasil->num_rows > 0) {
		    while($row = $hasil->fetch_assoc()) {
	?>
		<tr>
			<td align="left">
			<?php
				$sqlnis = "SELECT no_induk FROM users WHERE id='$row[id_user]'";
				$d_data = $conn->query($sqlnis);
				if ($d_data->num_rows > 0) {
				    while($data = $d_data->fetch_assoc()) {
				    	echo $data['no_induk'];
				    }
				}
			?>
			</td>
			<td><?php
				$sqlnama = "SELECT nama FROM users WHERE id='$row[id_user]'";
				$d_data = $conn->query($sqlnama);
				if ($d_data->num_rows > 0) {
				    while($data = $d_data->fetch_assoc()) {
				    	echo $data['nama'];
				    }
				}
			?></td>
			<td>
			<?php
				$q_jumlahsoal = "SELECT * FROM detailsoals WHERE id_soal='$id_soal' AND status='Y'";
				$d_jumlahsoal = $conn->query($q_jumlahsoal);
				echo $d_jumlahsoal->num_rows;
			?>
			</td>
			<td>
			<?php
				$q_jumlahbenar = "SELECT * FROM jawabs WHERE id_kelas='$id_kelas' AND id_soal='$id_soal' AND id_user='$row[id_user]' AND score!=0";
				$d_jumlahbenar = $conn->query($q_jumlahbenar);
				echo $d_jumlahbenar->num_rows;
			?>
			</td>
			<td>
			<?php
				$sql="SELECT sum(score) as total FROM jawabs WHERE id_kelas='$id_kelas' AND id_soal='$id_soal' AND id_user='$row[id_user]'";
				$result = $conn->query($sql);
				while ($row = mysqli_fetch_assoc($result))
				{
				   echo $row['total'];
				}
			?>
			</td>
		</tr>
	<?php
			}
		}
	?>
	</tbody>
</table>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
<?php
	include(app_path() . '/functions/koneksi.php');
	$id_kelas = Request::segment(2);
	$id_soal = Request::segment(3);

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
			<th style="text-align: center;">NIS</th>
			<th style="text-align: center;">Nama</th>
			<th style="text-align: center;">Jumlah Soal</th>
			<th style="text-align: center;">Jawaban Benar</th>
			<th style="text-align: center;">Nilai</th>
		</tr>
	</thead>
	<tbody>
		<?php
			/*$sql = $conn->query("SELECT * FROM jawabs WHERE id_soal='$id_soal' AND id_kelas='$id_kelas' AND status='Y'");*/
			$sql = $conn->query("SELECT * FROM jawabs WHERE id_soal='$id_soal' AND id_kelas='$id_kelas' AND status='Y' GROUP BY id_user");
			if ($sql->num_rows > 0) {
		    while($row = $sql->fetch_assoc()) {
		?>
		<tr>
			<td>
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
			<td>
				<?php
					$sqlnama = "SELECT nama FROM users WHERE id='$row[id_user]'";
					$d_data = $conn->query($sqlnama);
					if ($d_data->num_rows > 0) {
					    while($data = $d_data->fetch_assoc()) {
					    	echo $data['nama'];
					    }
					}
				?>
			</td>
			<td style="text-align: center;">
				<?php
					$q_jumlahsoal = $conn->query("SELECT * FROM detailsoals WHERE id_soal='$row[id_soal]' AND status='Y'");
					echo $q_jumlahsoal->num_rows;
				?>
			</td>
			<td style="text-align: center;">
				<?php
					$q_jumlahbenar = $conn->query("SELECT * FROM jawabs WHERE id_soal='$row[id_soal]' AND status='Y' AND score != 0 AND id_kelas='$row[id_kelas]' AND id_user='$row[id_user]'");
					echo $q_jumlahbenar->num_rows;
				?>
			</td>
			<td style="text-align: center;">
				<?php
					$sql_nilai = $conn->query("SELECT sum(score) as total FROM jawabs WHERE id_soal='$row[id_soal]' AND status='Y' AND score != 0 AND id_kelas='$row[id_kelas]' AND id_user='$row[id_user]'");
					while ($d_nilai = mysqli_fetch_assoc($sql_nilai))
					{
						echo $d_nilai['total'];
					}
				?>
			</td>
		</tr>
		<?php }} ?>
	</tbody>
</table>
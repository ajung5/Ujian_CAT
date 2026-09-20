<?php include(app_path().'/functions/koneksi.php'); ?>
<table class="table table-striped table-condensed" style="font-size: 11pt">
  <thead>
    <tr>
      <th class="center">#</th>
      <th>Paket Soal</th>
      <th>Jenis Soal</th>
      <th class="center">Nilai</th>
      <th class="center">KKM</th>
      <th class="center">Status</th>
      <th>Tanggal</th>
      <th class="center">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; ?>

    @if($jawabs->count())
      @foreach($jawabs as $jawab)
        <?php
          if ((int)$jawab->kkm <= (int)$jawab->count) {
            $status = "<span style='color:#009900; font-size:18px; font-weight:bold; text-align:center'>Lulus</span>";
          } else {
            $status = "<span style='color:#e60000; font-size:18px; font-weight:bold; text-align:center'>Tidak Lulus</span>";
          }

          if($jawab->created_at != "" and $jawab->created_at != "0000-00-00"){
            $tanggal = explode(" ", $jawab->created_at);
            $tanggal = explode("-", $tanggal[0]);
            $tanggal = $tanggal[2].' '.$bulanpendek[$tanggal[1]].' '.$tanggal[0];
          } else {
            $tanggal = 'tidak valid';
          }

          if ((int)$jawab->jenis_soal === 1) {
            $jenis = "<span style='color:#0441a3'>Ujian</span>";
          } else {
            $jenis = "<span style='color:#0493a3'>Latihan</span>";
          }
        ?>

        <tr>
          <td class="center">{{ $no++ }}</td>
          <td>{{ $jawab->paket }}</td>
          <td>{!! $jenis !!}</td>
          <td class="center"><strong>{{ $jawab->count }}</strong></td>
          <td class="center">{{ $jawab->kkm }}</td>
          <td class="center">{!! $status !!}</td>
          <td>{{ $tanggal }}</td>
          <td class="center">
            <a href="{{ url('/hasil-siswa/detail/'.$jawab->id_soal) }}"
               class="btn btn-sm btn-info"
               data-toggle="tooltip"
               title="Review jawaban yang telah dikirim">
              <i class="fa fa-eye"></i> Review Jawaban
            </a>
          </td>
        </tr>
      @endforeach
    @else
      <tr>
        <td colspan="8" class="alert alert-info">Data tidak ditemukan.</td>
      </tr>
    @endif
  </tbody>
</table>

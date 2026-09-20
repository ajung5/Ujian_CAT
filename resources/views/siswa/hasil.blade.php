@extends('layouts/siswa_baru')
@section('title', 'Hasil Ujian')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li class="active">Hasil Ujian</li>
@endsection
@section('content')
<?php include(app_path().'/functions/koneksi.php'); ?>
<div class="col-md-12">
  <div class="card">
    <div class="card-header bg-white">
      <div class="media">
        <div class="media-body">
          <h4 class="card-title">Hasil Ujian</h4>
        </div>
      </div>
    </div>

    <div style="padding: 15px">
      @if(Session::has('error'))
        <div class="alert alert-danger">
          {{ Session::get('error') }}
        </div>
      @endif

      @if(Session::has('success'))
        <div class="alert alert-success">
          {{ Session::get('success') }}
        </div>
      @endif

      <div class="form-inline" style="margin-bottom: 15px">
        <div class="input-group">
          {!! csrf_field() !!}
          <input id="q" type="text" class="form-control" placeholder="Search">
          <span class="input-group-btn">
            <button class="btn" type="button" id="search">
              <i class="fa fa-search" aria-hidden="true"></i>
            </button>
          </span>
        </div>
      </div>

      <div id="loading" style="display: none;">
        <img src="{{ url('/assets/assets/images/facebook.gif') }}" alt="loading">
      </div>

      <div id="wrap-hasil" style="overflow-x: auto;">
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
                <td colspan="8" class="alert alert-info">Belum ada data untuk ditampilkan.</td>
              </tr>
            @endif
          </tbody>
        </table>

        @if ($jawabs->lastPage() > 1)
          <ul class="pagination pagination-sm">
            <li class="{{ ($jawabs->currentPage() == 1) ? ' page-item disabled' : 'page-item' }}">
              <a class="page-link" href="{{ $jawabs->url(1) }}">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
              </a>
            </li>

            @for ($i = 1; $i <= $jawabs->lastPage(); $i++)
              <li class="{{ ($jawabs->currentPage() == $i) ? ' page-item active' : 'page-item' }}">
                <a class="page-link" href="{{ $jawabs->url($i) }}">{{ $i }}</a>
              </li>
            @endfor

            <li class="{{ ($jawabs->currentPage() == $jawabs->lastPage()) ? ' page-item disabled' : 'page-item' }}">
              <a class="page-link" href="{{ $jawabs->url($jawabs->currentPage()+1) }}">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
              </a>
            </li>
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script>
$(document).ready(function(){
  function cariHasil(){
    $("#wrap-hasil").hide();
    $("#loading").show();

    var q = encodeURIComponent($("#q").val());

    $.ajax({
      type: "POST",
      url: "{{ url('/get-hasil') }}",
      data: 'q='+q,
      success: function(data){
        $("#loading").hide();
        $("#wrap-hasil").html(data).fadeIn(250);
      },
      error: function(){
        $("#loading").hide();
        $("#wrap-hasil").show();
        alert('Pencarian hasil gagal. Silakan coba kembali.');
      }
    });
  }

  $("#q").keyup(function(e){
    if (e.keyCode == 13) {
      cariHasil();
    }
  });

  $("#search").click(function(){
    cariHasil();
  });
});
</script>
@endsection

@if($materis->count())
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
	</tbody>
</table>
@else
	<div class="alert alert-danger"><b>ERROR: </b>Kata kunci yang Anda masukan "<b>{{ $q }}</b>" tidak ditemukan.</div>
@endif
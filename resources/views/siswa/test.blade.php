<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ url('css/admin.css') }}">
<script src="{{url('lib/jquery/jquery.js')}}"></script>
<script src="{{url('lib/bootstrap/js/bootstrap.js')}}"></script>
<div class="container">
	<div class="content" style="background: #fff">
		<div id="wrap-soal">
			{{ $user->nama }}
		</div>
		<div id="nav-soal">
			<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
			<ul class="pagination">
				<?php $no = 1; ?>
				@if($users->count())
				@foreach($users as $data)
					<input type="hidden" id="id{{ $data->id }}" value="{{ $data->id }}">
					<li id="get-soal{{ $data->id }}"><a href="#" rel="next">{{ $no++ }}</a></li>
					<script>
						jQuery.noConflict()(function ($) {
							$(document).ready(function(){
								$("#get-soal{{ $data->id }}").click(function(){
									var id = $("#id{{ $data->id }}").val();
									$.ajax({
										type: "POST",
										url: "{{ url('/get-soal/'.$data->id) }}",
										data: 'id='+id,
										success: function(data){
											$("#wrap-soal").html(data);
										}
									})
								});
							});
						});
						$.ajaxSetup({
					    headers: {
					      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					    }
					  });
					</script>
				@endforeach
				@endif
			</ul>
		</div>

	</div>
</div>

<script>
	
</script>

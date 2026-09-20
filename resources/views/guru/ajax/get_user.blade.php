<table class="table table-bordered table-default table-striped nomargin" id="users-table">
  <thead>
    <tr>
      <th style="text-align: center;">#</th>
      <th>Nama</th>
      <th>NIP</th>
      <th>Email</th>
      <th>J.Kelamin</th>
      <th width="50px" style="text-align: center;">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = $users->firstItem(); ?>
    @if($users->count())
    @foreach($users as $data)
    <?php
      if ($data->jk == 'L') {
        $jk = 'Laki-laki';
      }else{
        $jk = 'Perempuan';
      }
    ?>
    <tr>
      <td style="text-align: center;">{{ $no++ }}</td>
      <td>{{ $data->nama }}</td>
      <td>{{ $data->no_induk }}</td>
      <td>{{ $data->email }}</td>
      <td>{{ $jk }}</td>
      <td style="text-align: center;">
        <a href="{{ url('/detail-guru/'.$data->id) }}" class="btn btn-primary btn-xs" title="Detail">Detail</a>
      </td>
    </tr>
    @endforeach
    @else
    <tr>
    	<td colspan="6" class="alert alert-danger">Kata kunci Anda: <b>{{ $q }}</b>, tidak ditemukan.</td>
    </tr>
    @endif
  </tbody>
</table>
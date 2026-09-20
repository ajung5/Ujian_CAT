<table class="table table-bordered table-default table-striped nomargin" id="users-table">
  <thead>
    <tr>
      <th>#</th>
      <th>Nama</th>
      <th>NIS</th>
      <th>Email</th>
      <th>J.Kelamin</th>
      <th>Kelas</th>
      <th width="50px">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = $users->firstItem(); ?>
    @if($users->count())
    @foreach($users as $data_user)
    <?php
      if ($data_user->jk == 'L') {
        $jk = 'Laki-laki';
      }else{
        $jk = 'Perempuan';
      }
    ?>
    <tr>
      <td>{{ $no++ }}</td>
      <td>{{ $data_user->nama }}</td>
      <td>{{ $data_user->no_induk }}</td>
      <td>{{ $data_user->email }}</td>
      <td>{{ $jk }}</td>
      <td>{{ $data_user->nama_kelas }}</td>
      <td>
        <a href="{{ url('/detail-kelas-siswa/'.$data_user->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Detail</a>
      </td>
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
<table
    class="
        table
        table-bordered
        table-default
        table-striped
        nomargin
    "
>

    <thead>

    <tr>

        <th>#</th>
        <th>Nama</th>
        <th>Status</th>
        <th>NIS / ID Pendaftaran</th>
        <th>Email</th>
        <th>J.Kelamin</th>
        <th>Kelas</th>
        <th width="70">Aksi</th>

    </tr>

    </thead>


    <tbody>

    @forelse ($users as $dataUser)

        <tr>

            <td>
                {{ $loop->iteration }}
            </td>

            <td>
                {{ $dataUser->nama }}
            </td>

            <td>
                @if ($dataUser->status === 'C')
                    <span class="label label-warning">
                        Calon Siswa
                    </span>
                @else
                    <span class="label label-success">
                        Siswa
                    </span>
                @endif
            </td>

            <td>
                {{ $dataUser->no_induk }}
            </td>

            <td>
                {{ $dataUser->email }}
            </td>

            <td>

                @if ($dataUser->jk === 'L')
                    Laki-laki
                @elseif ($dataUser->jk === 'P')
                    Perempuan
                @else
                    -
                @endif

            </td>

            <td>
                {{ $dataUser->nama_kelas ?: '-' }}
            </td>

            <td>

                <a
                    href="{{
                        route(
                            'guru.siswa.detail',
                            $dataUser->id
                        )
                    }}"
                    class="btn btn-xs btn-primary"
                >
                    <i class="fa fa-search"></i>

                    Detail
                </a>

            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="8"
                class="alert alert-danger"
            >

                Data dengan kata kunci

                <b>{{ $q }}</b>

                tidak ditemukan.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>
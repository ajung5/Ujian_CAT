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

        <th style="text-align:center;">
            #
        </th>

        <th>Nama</th>

        <th>NIP</th>

        <th>Email</th>

        <th>J.Kelamin</th>

        <th
            width="70"
            style="text-align:center;"
        >
            Aksi
        </th>

    </tr>

    </thead>


    <tbody>

    @forelse ($users as $data)

        <tr>

            <td style="text-align:center;">

                {{
                    $users->firstItem()
                    + $loop->index
                }}

            </td>

            <td>
                {{ $data->nama }}
            </td>

            <td>
                {{ $data->no_induk ?: '-' }}
            </td>

            <td>
                {{ $data->email }}
            </td>

            <td>

                @if ($data->jk === 'L')
                    Laki-laki
                @elseif ($data->jk === 'P')
                    Perempuan
                @else
                    -
                @endif

            </td>

            <td style="text-align:center;">

                <a
                    href="{{
                        route(
                            'guru.detail',
                            $data->id
                        )
                    }}"
                    class="btn btn-primary btn-xs"
                >
                    Detail
                </a>

            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="6"
                class="alert alert-danger"
            >

                Kata kunci Anda:

                <b>{{ $q }}</b>

                tidak ditemukan.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>
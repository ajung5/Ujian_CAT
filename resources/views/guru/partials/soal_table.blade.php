<table
    class="
        table
        table-bordered
        table-striped
        table-hover
        table-condensed
    "
>

    <thead>

    <tr>

        <th style="width:50px;">
            #
        </th>

        <th class="text-center">
            ID <small>Soal</small>
        </th>

        <th>
            Paket <small>Soal</small>
        </th>

        <th>
            Deskripsi
        </th>

        <th>
            KKM
        </th>

        <th>
            Waktu
        </th>

        <th>
            Tgl Dibuat
        </th>

        <th
            style="
                width:130px;
                text-align:center;
            "
        >
            Aksi
        </th>

    </tr>

    </thead>


    <tbody>

    @forelse ($soals as $soal)

        <tr>

            <td>

                {{
                    method_exists(
                        $soals,
                        'firstItem'
                    )
                        ? $soals->firstItem()
                            + $loop->index
                        : $loop->iteration
                }}

            </td>


            <td class="text-center">

                {{ $soal->id }}

            </td>


            <td>

                {{ $soal->paket }}

                @if ($soal->jenis === '2')

                    <span class="label label-info">
                        Latihan
                    </span>

                @else

                    <span class="label label-primary">
                        Ujian
                    </span>

                @endif

            </td>


            <td>

                {{ $soal->deskripsi }}

            </td>


            <td>

                {{ $soal->kkm }}

            </td>


            <td>

                {{
                    number_format(
                        ((int) $soal->waktu) / 60,
                        0
                    )
                }}

                menit

            </td>


            <td>

                {{
                    $soal->created_at
                        ? $soal
                            ->created_at
                            ->format('d M Y')
                        : '-'
                }}

            </td>


            <td class="text-center">

                <a
                    href="{{
                        route(
                            'guru.soal.edit',
                            $soal->id
                        )
                    }}"
                    class="
                        btn
                        btn-xs
                        btn-success
                    "
                    title="Ubah Soal"
                >

                    <i
                        class="
                            fa
                            fa-pencil-square-o
                        "
                    ></i>

                </a>


                <a
                    href="{{
                        url(
                            '/detail-soal/'.
                            $soal->id
                        )
                    }}"
                    class="
                        btn
                        btn-xs
                        btn-primary
                    "
                    title="Detail Soal"
                >

                    <i class="fa fa-search"></i>

                </a>
                <button
                    type="button"
                    class="
                        btn
                        btn-xs
                        btn-danger
                        js-delete-soal
                    "
                    data-url="{{
                        route(
                            'guru.soal.destroy',
                            $soal->id
                        )
                    }}"
                    data-paket="{{ $soal->paket }}"
                    title="Hapus Soal"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="8"
                class="alert alert-danger"
            >

                Belum ada data untuk ditampilkan.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>


@if (
    method_exists(
        $soals,
        'links'
    )
)

    {{
        $soals->links(
            'vendor.pagination.bootstrap-3'
        )
    }}

@endif
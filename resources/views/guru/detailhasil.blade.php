@extends('layouts.guru_baru')

@section('title', 'Hasil per Kelas')


@section('content')

<div
    class="
        col-sm-12
        col-md-8
        col-lg-8
        dash-left
    "
>

    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>

        <li>
            <a href="{{ route('guru.results') }}">
                Laporan
            </a>
        </li>

        <li class="active">
            {{ $soal->paket }}
        </li>

    </ol>


    <div class="panel panel-default">

        <div
            class="panel-heading"
            style="
                background:#072047;
                color:#fff;
            "
        >

            Laporan

            <b>
                {{ $soal->paket }}
            </b>

        </div>


        <div class="panel-body">

            <div class="alert alert-info">

                <i class="fa fa-info-circle"></i>

                Hasil pengerjaan dikelompokkan
                berdasarkan kelas.

            </div>


            <table
                class="table table-bordered"
                id="tabelsoal"
            >

                <thead>

                <tr>

                    <th>
                        Kelas
                    </th>

                    <th class="text-center">
                        Peserta
                    </th>

                    <th
                        class="text-center"
                        width="300"
                    >
                        Aksi
                    </th>

                </tr>

                </thead>


                <tbody>

                @forelse (
                    $jawabs
                    as $jawab
                )

                    <tr
                        id="class-row-{{
                            $jawab->id_kelas
                        }}"
                    >

                        <td>
                            {{ $jawab->nama_kelas }}
                        </td>


                        <td class="text-center">

                            {{
                                $jawab->jumlah_peserta
                            }}

                        </td>


                        <td class="text-center">

                            <a
                                href="{{
                                    route(
                                        'guru.results.class-detail',
                                        [
                                            'id' =>
                                                $jawab->id_kelas,

                                            'idSoal' =>
                                                $soal->id,
                                        ]
                                    )
                                }}"
                                class="
                                    btn
                                    btn-xs
                                    btn-primary
                                "
                            >

                                <i class="fa fa-search"></i>

                                Detail

                            </a>
                            <a
                                href="{{
                                    route(
                                        'guru.results.class.export',
                                        [
                                            'id' =>
                                                $jawab->id_kelas,

                                            'idSoal' =>
                                                $soal->id,
                                        ]
                                    )
                                }}"
                                class="
                                    btn
                                    btn-xs
                                    btn-success
                                "
                            >

                                <i class="fa fa-file-excel-o"></i>

                                Rekap Nilai

                            </a>
                            <a
                                href="{{
                                    route(
                                        'guru.results.class.display',
                                        [
                                            'id' =>
                                                $jawab->id_kelas,

                                            'idSoal' =>
                                                $soal->id,
                                        ]
                                    )
                                }}"
                                class="
                                    btn
                                    btn-xs
                                    btn-primary
                                "
                                target="_blank"
                            >

                                <i class="fa fa-desktop"></i>

                                Tampil

                            </a>


                            <button
                                type="button"
                                class="
                                    btn
                                    btn-xs
                                    btn-danger
                                    btn-delete-class
                                "
                                data-class-id="{{
                                    $jawab->id_kelas
                                }}"
                                data-exam-id="{{
                                    $soal->id
                                }}"
                            >

                                <i class="fa fa-trash-o"></i>

                                Hapus

                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            class="alert alert-info"
                        >

                            Belum ada hasil
                            untuk paket ini.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>


            @if (
                $jawabs->lastPage() > 1
            )

                {!! $jawabs->links() !!}

            @endif

        </div>

    </div>

</div>


<div
    class="
        col-sm-12
        col-md-4
        col-lg-4
        dash-right
    "
>

    <div class="panel panel-primary">

        <div class="panel-heading">

            <h4 class="panel-title">
                Aktifitas Terkini
            </h4>

        </div>


        <div class="panel-body">

            <ul class="media-list user-list">

                @foreach (
                    $aktifitas
                    as $data
                )

                    @php

                        $gambar =
                            ! empty(
                                $data->gambar
                            )
                                ? $data->gambar
                                : 'noimage.jpg';

                    @endphp


                    <li class="media">

                        <div class="media-left">

                            <img
                                class="
                                    media-object
                                    img-thumbnail
                                "
                                src="{{
                                    asset(
                                        'img/'.
                                        basename(
                                            $gambar
                                        )
                                    )
                                }}"
                                width="45"
                                alt=""
                            >

                        </div>


                        <div class="media-body">

                            <h4
                                class="
                                    media-heading
                                    nomargin
                                "
                            >

                                {{ $data->nama_user }}

                            </h4>

                            {{ $data->nama }}

                            <small class="date">

                                <i class="fa fa-clock-o"></i>

                                {{
                                    \Illuminate\Support\Carbon::parse(
                                        $data->created_at
                                    )->format(
                                        'd-m-Y'
                                    )
                                }}

                            </small>

                        </div>

                    </li>

                @endforeach

            </ul>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {

    $('[data-toggle="tooltip"]')
        .tooltip();


    $('.btn-delete-class')
        .on(
            'click',
            function () {

                if (
                    ! confirm(
                        'Yakin seluruh hasil kelas ini akan dihapus?'
                    )
                ) {
                    return;
                }


                const button =
                    $(this);

                const classId =
                    button.data('class-id');

                const examId =
                    button.data('exam-id');


                button.prop(
                    'disabled',
                    true
                );


                $.ajax({

                    type: 'POST',

                    url:
                        '{{
                            route(
                                'guru.results.class.destroy'
                            )
                        }}',

                    data: {
                        id_kelas:
                            classId,

                        id_soal:
                            examId
                    },


                    success: function (
                        response
                    ) {

                        $('#class-row-' + classId)
                            .fadeOut(
                                300,
                                function () {
                                    $(this).remove();
                                }
                            );

                    },


                    error: function (xhr) {

                        button.prop(
                            'disabled',
                            false
                        );


                        alert(
                            xhr.responseJSON
                                ?.message ||
                            'Hasil kelas gagal dihapus.'
                        );

                    }

                });

            }
        );

});

</script>

@endpush
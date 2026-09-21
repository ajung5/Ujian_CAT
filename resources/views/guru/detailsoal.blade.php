@extends('layouts.guru_baru')

@section('title', 'Detail Soal')


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('lib/summernote/summernote.css') }}"
>

<link
    rel="stylesheet"
    href="{{ asset('lib/select2/select2.css') }}"
>

<link
    rel="stylesheet"
    href="{{ asset('assets/js/dropzone/dropzone.css') }}"
>

@endpush


@section('content')

<div class="col-md-12 dash-left">

    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>

        <li>
            <a href="{{ route('guru.soal') }}">
                Soal
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
            Detail Paket Soal
        </div>


        <div class="panel-body">

            <button
                type="button"
                class="btn btn-primary"
                id="btsoal"
            >
                Tambah Soal
            </button>


            @if (
                (string) $soal->jenis === '1'
            )

                <button
                    type="button"
                    class="btn btn-primary"
                    id="btkelas"
                >
                    Lihat Kelas
                </button>

            @endif


            {{-- Distribusi kelas --}}
            @if (
                (string) $soal->jenis === '1'
            )

                <div
                    id="wrapdistribusisoal"
                    style="
                        margin-top:15px;
                        display:none;
                    "
                >

                    <div class="panel panel-default">

                        <div class="panel-body">

                            <h3 class="text-center">
                                Daftar Kelas
                            </h3>


                            <div class="alert alert-info">

                                <b>Perhatian!</b>

                                Pilih kelas yang dapat
                                mengakses paket ujian ini.

                            </div>


                            <div class="well">

                                @forelse (
                                    $kelas as $dataKelas
                                )

                                    <div class="checkbox">

                                        <label>

                                            <input
                                                type="checkbox"
                                                class="
                                                    kelas-distribusi
                                                "
                                                data-kelas="{{
                                                    $dataKelas->id
                                                }}"
                                                {{
                                                    in_array(
                                                        (string)
                                                        $dataKelas->id,
                                                        $kelasTerdistribusi,
                                                        true
                                                    )
                                                        ? 'checked'
                                                        : ''
                                                }}
                                            >

                                            {{
                                                $dataKelas->nama
                                            }}

                                        </label>

                                    </div>

                                @empty

                                    <div
                                        class="
                                            alert
                                            alert-warning
                                        "
                                    >
                                        Belum ada kelas.
                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </div>

                </div>

            @endif


            {{-- Form tambah soal --}}
            <div
                id="wrapsoal"
                style="
                    margin-top:15px;
                    display:none;
                "
            >

                <div
                    class="well"
                    style="background:#fff;"
                >

                    <div class="form-horizontal">


                        <input
                            type="hidden"
                            id="paket"
                            value="{{ $soal->id }}"
                        >


                        <input
                            type="hidden"
                            id="sesi"
                            value="{{ $sesiBaru }}"
                        >


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                            >
                                Soal
                            </label>

                            <div class="col-sm-10">

                                <textarea
                                    class="form-control"
                                    id="soal"
                                ></textarea>

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                            >
                                File Audio
                            </label>

                            <div class="col-sm-10">

                                <form
                                    action="{{
                                        route(
                                            'guru.soal.audio.upload'
                                        )
                                    }}"
                                    method="POST"
                                    class="dropzone"
                                    id="audio-dropzone"
                                >

                                    @csrf

                                    <input
                                        type="hidden"
                                        name="tampil"
                                        value="N"
                                    >

                                    <input
                                        type="hidden"
                                        name="sesi"
                                        value="{{ $sesiBaru }}"
                                    >

                                </form>

                            </div>

                        </div>


                        @foreach (
                            [
                                'pila' => 'Pilihan A',
                                'pilb' => 'Pilihan B',
                                'pilc' => 'Pilihan C',
                                'pild' => 'Pilihan D',
                                'pile' => 'Pilihan E',
                            ]
                            as $field => $label
                        )

                            <div class="form-group">

                                <label
                                    class="
                                        col-sm-2
                                        control-label
                                    "
                                >
                                    {{ $label }}
                                </label>

                                <div class="col-sm-10">

                                    <textarea
                                        class="form-control"
                                        id="{{ $field }}"
                                    ></textarea>

                                </div>

                            </div>

                        @endforeach


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                            >
                                Kunci
                            </label>

                            <div class="col-sm-10">

                                <select
                                    id="kunci"
                                    class="form-control"
                                >

                                    <option value="">
                                        -- Pilih Kunci Jawaban --
                                    </option>

                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>

                                </select>

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                            >
                                Score
                            </label>

                            <div class="col-sm-10">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="score"
                                >

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                            >
                                Status
                            </label>

                            <div class="col-sm-10">

                                <select
                                    id="status"
                                    class="form-control"
                                >

                                    <option value="">
                                        -- Pilih Status Soal --
                                    </option>

                                    <option value="Y">
                                        Tampil
                                    </option>

                                    <option value="N">
                                        Tidak Tampil
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="form-group">

                            <div
                                class="
                                    col-sm-offset-2
                                    col-sm-10
                                "
                            >

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    id="btnsimpansoal"
                                >
                                    Simpan
                                </button>

                                <img
                                    src="{{
                                        asset(
                                            'img/ajax-loader.gif'
                                        )
                                    }}"
                                    id="loading"
                                    alt="Loading"
                                    style="display:none;"
                                >

                            </div>

                        </div>


                        <div
                            class="
                                alert
                                alert-danger
                            "
                            id="salah"
                            style="display:none;"
                        ></div>


                        <div
                            class="
                                alert
                                alert-info
                            "
                            id="benar"
                            style="display:none;"
                        >
                            <b>Sukses.</b>
                            Soal berhasil dibuat.
                        </div>

                    </div>

                </div>

            </div>


            <hr>


            <div class="table-responsive">

                <table
                    class="
                        table
                        table-condensed
                        table-hover
                        table-bordered
                    "
                >

                    <caption>

                        Daftar soal untuk paket

                        <b>
                            {{ $soal->paket }}
                        </b>

                    </caption>


                    <thead>

                    <tr>

                        <th style="width:50px;">
                            NO
                        </th>

                        <th>
                            Soal
                        </th>

                        <th class="text-center">
                            Kunci
                        </th>

                        <th class="text-center">
                            Score
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                        <th
                            class="text-center"
                            style="width:120px;"
                        >
                            Aksi
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse (
                        $detailsoals
                        as $detail
                    )

                        <tr
                            id="detail-{{
                                $detail->id
                            }}"
                        >

                            <td>
                                {{ $loop->iteration }}
                            </td>


                            <td>

                                {!! $detail->soal !!}

                                @if (
                                    ! empty(
                                        $detail->audio
                                    )
                                )

                                    <div
                                        style="
                                            margin-top:10px;
                                        "
                                    >

                                        <audio controls>

                                            <source
                                                src="{{
                                                    asset(
                                                        'assets/audios/'.
                                                        $detail->audio
                                                    )
                                                }}"
                                            >

                                        </audio>

                                    </div>

                                @endif

                            </td>


                            <td class="text-center">
                                {{ $detail->kunci }}
                            </td>


                            <td class="text-center">
                                {{ $detail->score }}
                            </td>


                            <td class="text-center">

                                @if (
                                    $detail->status
                                    === 'Y'
                                )

                                    <span
                                        class="
                                            label
                                            label-success
                                        "
                                    >
                                        Tampil
                                    </span>

                                @else

                                    <span
                                        class="
                                            label
                                            label-danger
                                        "
                                    >
                                        Tidak
                                    </span>

                                @endif

                            </td>


                            <td class="text-center">

                                <a
                                    href="{{
                                        route(
                                            'guru.soal.detail.edit',
                                            $detail->id
                                        )
                                    }}"
                                >
                                    Ubah
                                </a>

                                |

                                <a
                                    href="#"
                                    class="
                                        hapus-detail
                                    "
                                    data-id="{{
                                        $detail->id
                                    }}"
                                >
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="
                                    alert
                                    alert-danger
                                "
                            >
                                Belum ada data untuk
                                ditampilkan.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('lib/select2/select2.js') }}"></script>

<script src="{{ asset('lib/summernote/summernote.js') }}"></script>

<script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>


<script>

Dropzone.autoDiscover = false;


$(document).ready(function () {

    'use strict';


    $('#soal').summernote({
        height: 150
    });

    $('#pila').summernote({
        height: 100
    });

    $('#pilb').summernote({
        height: 100
    });

    $('#pilc').summernote({
        height: 100
    });

    $('#pild').summernote({
        height: 100
    });

    $('#pile').summernote({
        height: 100
    });


    $('#kunci').select2();

    $('#status').select2();


    $('#btsoal').on(
        'click',
        function () {

            $('#wrapsoal')
                .slideToggle();

        }
    );


    $('#btkelas').on(
        'click',
        function () {

            $('#wrapdistribusisoal')
                .slideToggle();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Upload Audio
    |--------------------------------------------------------------------------
    */

    if (
        document.querySelector(
            '#audio-dropzone'
        )
    ) {

        new Dropzone(
            '#audio-dropzone',
            {
                paramName: 'file',

                maxFiles: 1,

                maxFilesize: 10,

                headers: {

                    'X-CSRF-TOKEN':
                        $('meta[name="csrf-token"]')
                            .attr('content')

                },

                success: function (
                    file,
                    response
                ) {

                    console.log(
                        'Audio uploaded:',
                        response
                    );

                },

                error: function (
                    file,
                    response
                ) {

                    console.error(
                        'Audio upload error:',
                        response
                    );

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Simpan Detail Soal
    |--------------------------------------------------------------------------
    */

    $('#btnsimpansoal').on(
        'click',
        function () {

            const button =
                $(this);

            button.hide();

            $('#loading').show();

            $('#salah').hide();


            $.ajax({

                type: 'POST',

                url:
                    '{{ route(
                        'guru.soal.detail.store'
                    ) }}',

                data: {

                    paket:
                        $('#paket').val(),

                    sesi:
                        $('#sesi').val(),

                    soal:
                        $('#soal').code(),

                    pila:
                        $('#pila').code(),

                    pilb:
                        $('#pilb').code(),

                    pilc:
                        $('#pilc').code(),

                    pild:
                        $('#pild').code(),

                    pile:
                        $('#pile').code(),

                    kunci:
                        $('#kunci').val(),

                    score:
                        $('#score').val(),

                    status:
                        $('#status').val()

                },


                success: function (data) {

                    $('#loading').hide();

                    button.show();


                    if (
                        $.trim(data) ===
                        'berhasil'
                    ) {

                        $('#benar').show();

                        window.location.reload();

                    }

                },


                error: function (xhr) {

                    $('#loading').hide();

                    button.show();


                    console.error(
                        'Simpan Detail Soal:',
                        xhr.status,
                        xhr.responseJSON,
                        xhr.responseText
                    );


                    let message =
                        'Gagal menyimpan soal.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.errors
                    ) {

                        message =
                            Object.values(
                                xhr.responseJSON.errors
                            )
                                .flat()
                                .join('<br>');

                    } else if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    $('#salah')
                        .html(message)
                        .show();

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Hapus Detail
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.hapus-detail',
        function (event) {

            event.preventDefault();


            if (
                ! confirm(
                    'Yakin soal ini akan dihapus?'
                )
            ) {
                return;
            }


            const id =
                $(this).data('id');


            $.ajax({

                type: 'POST',

                url:
                    '{{ route(
                        'guru.soal.detail.destroy'
                    ) }}',

                data: {
                    id_soal: id
                },


                success: function () {

                    $('#detail-' + id)
                        .fadeOut(250);

                },


                error: function (xhr) {

                    console.error(
                        xhr.responseText
                    );

                    alert(
                        'Gagal menghapus soal.'
                    );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Distribusi Kelas
    |--------------------------------------------------------------------------
    */

    $('.kelas-distribusi')
        .on(
            'change',
            function () {

                const checkbox =
                    $(this);

                const idKelas =
                    checkbox.data('kelas');

                const url =
                    checkbox.is(':checked')
                        ? '{{ route(
                            'guru.soal.distribution.store'
                        ) }}'
                        : '{{ route(
                            'guru.soal.distribution.destroy'
                        ) }}';


                $.ajax({

                    type: 'POST',

                    url: url,

                    data: {

                        id_soal:
                            '{{ $soal->id }}',

                        id_kelas:
                            idKelas

                    },


                    error: function (xhr) {

                        /*
                         * Kembalikan checkbox
                         * jika request gagal.
                         */
                        checkbox.prop(
                            'checked',
                            ! checkbox
                                .is(':checked')
                        );


                        console.error(
                            xhr.responseText
                        );


                        alert(
                            'Gagal mengubah ' +
                            'distribusi kelas.'
                        );

                    }

                });

            }
        );

});

</script>

@endpush
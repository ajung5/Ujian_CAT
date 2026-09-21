@extends('layouts.guru_baru')

@section('title', 'Ubah Materi')


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('lib/dropzone/dropzone.css') }}"
>

<link
    rel="stylesheet"
    href="{{ asset('lib/summernote/summernote.css') }}"
>

@endpush


@section('content')


<div class="col-sm-12 col-md-8 col-lg-8 dash-left">

    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>

        <li>
            <a href="{{ route('guru.materi') }}">
                Materi
            </a>
        </li>

        <li class="active">
            Ubah: {{ $materi->judul }}
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
            Lengkapi form di bawah ini.
        </div>


        <div class="panel-body">

            <div class="form-horizontal">


                <div class="form-group">

                    <label
                        class="col-sm-2 control-label"
                        for="judul"
                    >
                        Judul
                    </label>

                    <div class="col-sm-10">

                        <input
                            type="text"
                            class="form-control"
                            id="judul"
                            value="{{ $materi->judul }}"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label
                        class="col-sm-2 control-label"
                        for="isi"
                    >
                        Isi
                    </label>

                    <div class="col-sm-10">

                        <textarea
                            class="form-control"
                            id="isi"
                        >{{ $materi->isi }}</textarea>

                    </div>

                </div>


                <div class="form-group">

                    <label
                        class="col-sm-2 control-label"
                    >
                        Gambar
                    </label>

                    <div class="col-sm-10">

                        @if (! empty($materi->gambar))

                            <img
                                src="{{
                                    asset(
                                        'img/materi/'.
                                        $materi->gambar
                                    )
                                }}"
                                class="
                                    img
                                    img-thumbnail
                                "
                                style="
                                    width:250px;
                                    margin-bottom:15px;
                                "
                                alt="{{ $materi->judul }}"
                            >

                        @endif


                        <form
                            action="{{
                                route(
                                    'guru.materi.image'
                                )
                            }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="dropzone"
                            id="materi-edit-dropzone"
                        >

                            @csrf

                            <input
                                type="hidden"
                                id="sesi"
                                name="sesi"
                                value="{{ $materi->sesi }}"
                            >

                            <div class="fallback">

                                <input
                                    name="file"
                                    type="file"
                                    accept="
                                        image/jpeg,
                                        image/png,
                                        image/webp
                                    "
                                >

                            </div>

                        </form>

                    </div>

                </div>


                <div class="form-group">

                    <label
                        class="col-sm-2 control-label"
                    >
                        Status
                    </label>

                    <div class="col-sm-10">

                        <label class="radio-inline">

                            <input
                                type="radio"
                                name="status"
                                value="N"
                                @checked(
                                    $materi->status === 'N'
                                )
                            >

                            Tidak tampil

                        </label>


                        <label class="radio-inline">

                            <input
                                type="radio"
                                name="status"
                                value="Y"
                                @checked(
                                    $materi->status === 'Y'
                                )
                            >

                            Tampil

                        </label>

                    </div>

                </div>


                <div class="form-group">

                    <div
                        class="
                            col-sm-offset-2
                            col-sm-10
                        "
                    >

                        <img
                            src="{{
                                asset(
                                    'assets/assets/images/facebook.gif'
                                )
                            }}"
                            alt="Loading"
                            id="loading"
                            style="display:none;"
                        >


                        <div
                            id="notif"
                            style="display:none;"
                        ></div>


                        <button
                            type="button"
                            id="simpan"
                            class="btn btn-success btn-sm"
                        >
                            Simpan
                        </button>


                        <a
                            href="{{
                                route(
                                    'guru.materi.detail',
                                    $materi->id
                                )
                            }}"
                            class="btn btn-danger btn-sm"
                        >
                            Batal
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


@include('guru.partials.aktivitas', [
    'aktifitas' => $aktifitas
])

@endsection


@push('scripts')

<script src="{{ asset('lib/dropzone/dropzone.js') }}"></script>

<script src="{{ asset('lib/summernote/summernote.js') }}"></script>


<script>

Dropzone.autoDiscover = false;


$(document).ready(function () {

    'use strict';


    $('#isi').summernote({
        height: 150
    });


    new Dropzone(
        '#materi-edit-dropzone',
        {
            paramName: 'file',

            maxFiles: 1,

            maxFilesize: 5,

            acceptedFiles:
                'image/jpeg,image/png,image/webp',

            headers: {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]')
                        .attr('content')
            },

            success: function () {

                window.location.reload();

            }
        }
    );


    $('#simpan').on(
        'click',
        function () {

            $('#loading').show();
            $('#notif').hide();


            $.ajax({

                type: 'POST',

                url:
                    '{{ route('guru.materi.save') }}',

                data: {

                    sesi:
                        $('#sesi').val(),

                    judul:
                        $('#judul').val(),

                    isi: $('#isi').code(),

                    status:
                        $(
                            'input[name="status"]:checked'
                        ).val()

                },

                success: function (data) {

                    $('#loading').hide();


                    if (data === 'ok') {

                        $('#notif')
                            .addClass(
                                'alert alert-info'
                            )
                            .html(
                                'Materi berhasil diubah.'
                            )
                            .show();


                        window.location.href =
                            '{{
                                route(
                                    'guru.materi.detail',
                                    $materi->id
                                )
                            }}';

                    }

                },

                error: function (xhr) {

                    $('#loading').hide();


                    let message =
                        'Gagal memperbarui materi.';


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

                    }


                    $('#notif')
                        .removeClass(
                            'alert-info'
                        )
                        .addClass(
                            'alert alert-danger'
                        )
                        .html(message)
                        .show();

                }

            });

        }
    );

});

</script>

@endpush
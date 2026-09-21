@extends('layouts.guru_baru')

@section('title', 'Edit Soal')


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
            Edit Soal
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
            Ubah Paket Soal
        </div>


        <div class="panel-body">

            <div
                class="well"
                style="
                    margin:0;
                    padding:15px;
                "
            >

                <div class="form-horizontal">


                    <input
                        type="hidden"
                        id="id_soal"
                        value="{{ $soal->id }}"
                    >


                    <div class="form-group">

                        <label
                            class="
                                col-sm-2
                                control-label
                            "
                            for="paket"
                        >
                            Paket
                        </label>


                        <div class="col-sm-10">

                            <input
                                type="text"
                                class="form-control"
                                id="paket"
                                value="{{ $soal->paket }}"
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label
                            class="
                                col-sm-2
                                control-label
                            "
                            for="deskripsi"
                        >
                            Deskripsi
                        </label>


                        <div class="col-sm-10">

                            <textarea
                                class="form-control"
                                id="deskripsi"
                            >{{ $soal->deskripsi }}</textarea>

                        </div>

                    </div>


                    <div class="form-group">

                        <label
                            class="
                                col-sm-2
                                control-label
                            "
                            for="kkm"
                        >
                            KKM
                        </label>


                        <div class="col-sm-10">

                            <input
                                type="number"
                                class="form-control"
                                id="kkm"
                                min="0"
                                value="{{ $soal->kkm }}"
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label
                            class="
                                col-sm-2
                                control-label
                            "
                            for="waktu"
                        >
                            Waktu
                        </label>


                        <div class="col-sm-10">

                            <input
                                type="number"
                                class="form-control"
                                id="waktu"
                                min="1"
                                value="{{ $soal->waktu }}"
                            >

                            <small class="help-block">

                                Waktu disimpan dalam detik.

                                Saat ini:

                                {{
                                    number_format(
                                        ((int) $soal->waktu)
                                        / 60,
                                        0
                                    )
                                }}

                                menit.

                            </small>

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
                                id="btn-update-soal"
                            >
                                Simpan
                            </button>


                            <a
                                href="{{
                                    route('guru.soal')
                                }}"
                                class="btn btn-default"
                            >
                                Batal
                            </a>


                            <img
                                src="{{
                                    asset(
                                        'img/ajax-loader.gif'
                                    )
                                }}"
                                alt="Loading"
                                id="loading"
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

                        Soal berhasil diperbarui.

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {

    'use strict';


    $('#btn-update-soal').on(
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
                    '{{ route('guru.soal.update') }}',

                data: {

                    id_soal:
                        $('#id_soal').val(),

                    paket:
                        $('#paket').val(),

                    deskripsi:
                        $('#deskripsi').val(),

                    kkm:
                        $('#kkm').val(),

                    waktu:
                        $('#waktu').val()

                },


                success: function (data) {

                    $('#loading').hide();

                    button.show();


                    if (
                        $.trim(data) ===
                        'berhasil'
                    ) {

                        $('#benar').show();


                        window.location.href =
                            '{{ route('guru.soal') }}';

                    }

                },


                error: function (xhr) {

                    $('#loading').hide();

                    button.show();


                    console.error(
                        'Update Paket Soal:',
                        xhr.status,
                        xhr.responseJSON,
                        xhr.responseText
                    );


                    let message =
                        'Gagal memperbarui paket soal.';


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

});

</script>

@endpush
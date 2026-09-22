@extends('layouts.guru_baru')

@section('title', 'Soal')

@section('content')

    <div class="col-md-12 dash-left">
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('guru.index') }}">
                    Home
                </a>
            </li>

            <li class="active">
                Soal
            </li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div id="soal-notification"></div>

        @if ($errors->any())

            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>
                        {{ $error }}
                    </div>
                @endforeach
            </div>

        @endif

        @if (session('import_errors') && count(session('import_errors')))

            <div class="alert alert-warning">
                <b>
                    Beberapa baris tidak diimport:
                </b>

                <ul style="
                    margin-top:10px;
                    margin-bottom:0;
                ">
                    @foreach (session('import_errors') as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>

        @endif

        <div class="panel panel-default">
            <div class="panel-heading"
                style="
                background:#072047;
                color:#fff;
            ">
                Data Soal
            </div>

            <div class="panel-body">
                <button type="button" class="btn btn-primary" id="btnsoal">
                    <i
                        class="
                        fa
                        fa-pencil-square-o
                    "></i>

                    Buat Paket Soal
                </button>

                <a href="{{ asset('readfile/soal.xls') }}" target="_blank" class="btn btn-success">
                    <i
                        class="
                        fa
                        fa-cloud-download
                    "></i>

                    Download Format Excel
                </a>
                <button type="button" class="btn btn-success" id="btnupload">
                    <i class="fa fa-cloud-upload"></i>
                    Upload Excel
                </button>
                <div id="uploadexcel"
                    style="
                    margin-top:15px;
                    display:none;
                ">
                    <div class="well">
                        <form action="{{ route('guru.soal.import') }}" method="POST" enctype="multipart/form-data"
                            class="form-horizontal">
                            @csrf

                            <div class="form-group">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="file">
                                    File Excel
                                </label>

                                <div class="col-sm-10">
                                    <input type="file" name="file" id="file" class="form-control"
                                        accept=".xls,.xlsx" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div
                                    class="
                                    col-sm-offset-2
                                    col-sm-10
                                ">
                                    <button type="submit" class="btn btn-primary">
                                        <i
                                            class="
                                            fa
                                            fa-cloud-upload
                                        "></i>

                                        Upload
                                    </button>
                                </div>
                            </div>

                            <div
                                class="
                                alert
                                alert-warning
                            ">
                                <b>PERHATIAN:</b>

                                Gunakan format Excel yang
                                disediakan.

                                Jangan mengubah urutan kolom.

                                Kolom:

                                <b>
                                    ID Paket,
                                    Soal,
                                    A,
                                    B,
                                    C,
                                    D,
                                    E,
                                    Kunci,
                                    Score
                                </b>.
                            </div>
                        </form>
                    </div>
                </div>
                <div id="wrapsoal"
                    style="
                    margin-top:15px;
                    display:none;
                ">
                    <div class="well">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="jenis">
                                    Jenis
                                </label>

                                <div class="col-sm-10">
                                    <select id="jenis" class="form-control">
                                        <option value="">
                                            -- Pilih jenis soal --
                                        </option>

                                        <option value="1">
                                            Ujian
                                        </option>

                                        <option value="2">
                                            Latihan
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="wrap-materi" style="display:none;">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="materi">
                                    Materi
                                </label>

                                <div class="col-sm-10">
                                    <select id="materi" class="form-control">
                                        <option value="">
                                            -- Pilih materi --
                                        </option>

                                        @forelse ($materis as $dataMateri)
                                            <option value="{{ $dataMateri->id }}">
                                                {{ $dataMateri->judul }}
                                            </option>

                                        @empty

                                            <option value="">
                                                Anda belum memiliki
                                                materi yang tersedia.
                                            </option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="paket">
                                    Paket
                                </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="paket"
                                        placeholder="
                                        Paket soal,
                                        misal: UTS KKPI Kelas XI
                                    ">
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="deskripsi">
                                    Deskripsi
                                </label>

                                <div class="col-sm-10">
                                    <textarea class="form-control" id="deskripsi" placeholder="Deskripsi"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="kkm">
                                    KKM
                                </label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="kkm" min="0"
                                        placeholder="
                                        KKM,
                                        tuliskan dengan bilangan bulat
                                    ">
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="
                                    col-sm-2
                                    control-label
                                "
                                    for="waktu">
                                    Waktu
                                </label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="waktu" min="1"
                                        placeholder="
                                        Waktu dalam detik.
                                        Misal 60 menit = 3600
                                    ">
                                </div>
                            </div>

                            <div class="form-group">
                                <div
                                    class="
                                    col-sm-offset-2
                                    col-sm-10
                                ">
                                    <button type="button"
                                        class="
                                        btn
                                        btn-primary
                                    "
                                        id="btnsimpansoalpaket">
                                        Simpan
                                    </button>

                                    <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading" id="loading"
                                        style="display:none;">
                                </div>
                            </div>

                            <div class="
                                alert
                                alert-danger
                            "
                                id="salah" style="display:none;"></div>

                            <div class="
                                alert
                                alert-info
                            "
                                id="benar" style="display:none;">
                                <b>Sukses.</b>

                                Paket soal berhasil dibuat.
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="form-horizontal" style="margin-bottom:15px;">
                    <input type="text" class="form-control" id="q"
                        placeholder="
                        Cari berdasarkan Paket soal
                        (Ketik lalu Enter)
                    ">
                </div>

                <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="Loading" id="loading_cari"
                    style="display:none;">

                <div id="wrap-user" class="table-responsive">
                    @include('guru.partials.soal_table', [
                        'soals' => $soals,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            'use strict';


            $('#loading').hide();

            $('#salah').hide();

            $('#benar').hide();

            /*
            |--------------------------------------------------------------------------
            | Toggle Form Paket Soal & Upload Excel
            |--------------------------------------------------------------------------
            */

            $('#btnsoal').on(
                'click',
                function() {

                    $('#uploadexcel')
                        .stop(true, true)
                        .slideUp();

                    $('#wrapsoal')
                        .stop(true, true)
                        .slideToggle();

                }
            );


            $('#btnupload').on(
                'click',
                function() {

                    $('#wrapsoal')
                        .stop(true, true)
                        .slideUp();

                    $('#uploadexcel')
                        .stop(true, true)
                        .slideToggle();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Jenis Soal
            |--------------------------------------------------------------------------
            */

            $('#jenis').on(
                'change',
                function() {

                    const jenis =
                        $(this).val();


                    if (jenis === '2') {

                        $('#wrap-materi')
                            .slideDown();

                    } else {

                        $('#materi').val('');

                        $('#wrap-materi')
                            .slideUp();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Simpan Paket Soal
            |--------------------------------------------------------------------------
            */

            $('#btnsimpansoalpaket').on(
                'click',
                function() {

                    const button =
                        $(this);


                    button.hide();

                    $('#loading').show();

                    $('#salah').hide();

                    $('#benar').hide();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.soal.store') }}',

                        data: {

                            jenis: $('#jenis').val(),

                            materi: $('#materi').val(),

                            paket: $('#paket').val(),

                            deskripsi: $('#deskripsi').val(),

                            kkm: $('#kkm').val(),

                            waktu: $('#waktu').val()

                        },


                        success: function(data) {

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


                        error: function(xhr) {

                            $('#loading').hide();

                            button.show();


                            console.error(
                                'Simpan Paket Soal:',
                                xhr.status,
                                xhr.responseJSON,
                                xhr.responseText
                            );


                            let message =
                                'Gagal menyimpan paket soal.';


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

                            } else if (
                                xhr.responseText
                            ) {

                                /*
                                 * Untuk response legacy custom
                                 * seperti "Anda belum memilih materi".
                                 */
                                message =
                                    xhr.responseText;

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
            | Search Paket Soal
            |--------------------------------------------------------------------------
            */

            $('#q').on(
                'keyup',
                function(event) {

                    if (event.key !== 'Enter') {
                        return;
                    }


                    $('#loading_cari').show();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.soal.search') }}',

                        data: {

                            q: $('#q').val()

                        },


                        success: function(data) {

                            $('#loading_cari').hide();


                            $('#wrap-user')
                                .hide()
                                .html(data)
                                .fadeIn(300);

                        },


                        error: function(xhr) {

                            $('#loading_cari').hide();


                            console.error(
                                'Search Paket Soal:',
                                xhr.status,
                                xhr.responseText
                            );


                            alert(
                                'Gagal mencari paket soal.'
                            );

                        }

                    });

                }
            );

            /*
            |--------------------------------------------------------------------------
            | Hapus Paket Soal
            |--------------------------------------------------------------------------
            */

            $(document).on(
                'click',
                '.js-delete-soal',
                function() {

                    const button =
                        $(this);

                    const paket =
                        button.data('paket');

                    const url =
                        button.data('url');

                    if (
                        !window.confirm(
                            'Yakin akan menghapus paket soal "' +
                            paket +
                            '"?'
                        )
                    ) {
                        return;
                    }

                    button.prop(
                        'disabled',
                        true
                    );


                    $.ajax({

                        type: 'POST',

                        url: url,

                        headers: {
                            Accept: 'application/json'
                        },


                        success: function(response) {

                            const row =
                                button.closest('tr');

                            row.fadeOut(
                                200,
                                function() {
                                    $(this).remove();
                                }
                            );


                            $('#soal-notification')
                                .html(
                                    '<div class="alert alert-success">' +
                                    $('<div>')
                                    .text(
                                        response.message ||
                                        'Paket soal berhasil dihapus.'
                                    )
                                    .html() +
                                    '</div>'
                                )
                                .hide()
                                .fadeIn(150);


                            setTimeout(
                                function() {

                                    $('#soal-notification')
                                        .fadeOut(200);

                                },
                                3000
                            );

                        },


                        error: function(xhr) {

                            button.prop(
                                'disabled',
                                false
                            );


                            const message =
                                xhr.responseJSON &&
                                xhr.responseJSON.message ?
                                xhr.responseJSON.message :
                                'Paket soal gagal dihapus.';


                            $('#soal-notification')
                                .html(
                                    '<div class="alert alert-danger">' +
                                    $('<div>')
                                    .text(message)
                                    .html() +
                                    '</div>'
                                )
                                .hide()
                                .fadeIn(150);

                        }

                    });

                }
            );

        });
    </script>
@endpush

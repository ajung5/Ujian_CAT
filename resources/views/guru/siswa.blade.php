@extends('layouts.guru_baru')

@section('title', 'Data Siswa')


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/upload.css') }}"
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

        <li class="active">
            Data Siswa
        </li>

    </ol>


    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    @if ($errors->any())

        <div class="alert alert-danger">

            <b>Import gagal:</b>

            <ul style="margin-top:10px;">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif

    @if (
        session('import_errors') &&
        count(session('import_errors')) > 0
    )

        <div class="alert alert-warning">

            <b>
                Beberapa baris tidak diimport:
            </b>

            <ul
                style="
                    margin-top:10px;
                    max-height:250px;
                    overflow:auto;
                "
            >

                @foreach (
                    session('import_errors')
                    as $importError
                )

                    <li>
                        {{ $importError }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif
    <div class="panel panel-default">

        <div
            class="panel-heading"
            style="
                background:#072047;
                color:#fff;
            "
        >
            Data Siswa
        </div>


        <div class="panel-body">


            <button
                type="button"
                class="btn btn-primary"
                data-toggle="collapse"
                data-target="#wrapsiswa"
            >

                Tambah

            </button>


            <a
                href="{{ asset('readfile/data.xls') }}"
                target="_blank"
                class="btn btn-success"
            >
                <i class="fa fa-download"></i>
                Excel
            </a>
            <button
                type="button"
                class="btn btn-success"
                data-toggle="collapse"
                data-target="#uploadexcel"
            >
                <i class="fa fa-upload"></i>

                Siswa via Excel
            </button>
            <a
                href="{{ asset('readfile/datacalon.xls') }}"
                target="_blank"
                class="btn btn-success"
            >
                <i class="fa fa-download"></i>
                Excel Calon Siswa
            </a>
            <button
                type="button"
                class="btn btn-success"
                data-toggle="collapse"
                data-target="#uploadexcelcalonsiswa"
            >
                <i class="fa fa-upload"></i>

                Calon Siswa via Excel
            </button>

            <form
                method="POST"
                action="{{
                    route(
                        'guru.siswa.candidates.destroy'
                    )
                }}"
                style="display:inline;"
                onsubmit="
                    return confirm(
                        'Yakin seluruh data peserta PSB akan dihapus? ' +
                        'Data ujian calon siswa juga akan dihapus.'
                    );
                "
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Hapus Seluruh Calon Siswa
                </button>

            </form>


            <div
                class="collapse in"
                id="wrapsiswa"
                style="margin:15px 0 0 0;"
            >

                <div class="well">

                    <form
                        id="form-tambah-siswa"
                        class="form-horizontal"
                    >

                        @csrf


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                                for="nama"
                            >
                                Nama
                            </label>

                            <div class="col-sm-10">

                                <input
                                    type="text"
                                    class="form-control"
                                    name="nama"
                                    id="nama"
                                    placeholder="Nama"
                                >

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                                for="no_induk"
                            >
                                NIS
                            </label>

                            <div class="col-sm-10">

                                <input
                                    type="text"
                                    class="form-control"
                                    name="no_induk"
                                    id="no_induk"
                                    placeholder="NIS"
                                >

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                                for="email"
                            >
                                Email
                            </label>

                            <div class="col-sm-10">

                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    id="email"
                                    placeholder="Email"
                                >

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                                for="jk"
                            >
                                Jenis Kelamin
                            </label>

                            <div class="col-sm-10">

                                <select
                                    name="jk"
                                    id="jk"
                                    class="form-control"
                                >

                                    <option value="">
                                        -- Pilih Jenis Kelamin --
                                    </option>

                                    <option value="L">
                                        Laki-laki
                                    </option>

                                    <option value="P">
                                        Perempuan
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="form-group">

                            <label
                                class="
                                    col-sm-2
                                    control-label
                                "
                                for="id_kelas"
                            >
                                Kelas
                            </label>

                            <div class="col-sm-10">

                                <select
                                    name="id_kelas"
                                    id="id_kelas"
                                    class="form-control"
                                >

                                    <option value="">
                                        -- Pilih Kelas --
                                    </option>

                                    @foreach ($kelas as $daftarkelas)

                                        <option
                                            value="{{ $daftarkelas->id }}"
                                        >
                                            {{ $daftarkelas->nama }}
                                        </option>

                                    @endforeach

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
                                    type="submit"
                                    class="btn btn-primary"
                                    id="btnsimpansiswa"
                                >
                                    Simpan
                                </button>

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
                            class="alert alert-danger"
                            id="salah"
                            style="display:none;"
                        ></div>


                        <div
                            class="alert alert-info"
                            id="benar"
                            style="display:none;"
                        >

                            <b>Sukses.</b>

                            Data siswa berhasil disimpan.

                            Siswa dapat login menggunakan
                            password awal:

                            <strong>123456</strong>

                        </div>

                    </form>

                </div>

            </div>
            
            <div
                class="collapse"
                id="uploadexcel"
                style="margin:15px 0 0 0;"
            >

                <div class="well">

                    <form
                        action="{{ route('guru.siswa.import') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal"
                    >

                        @csrf


                        <div class="form-group">

                            <label
                                class="col-sm-2 control-label"
                                for="file_excel_siswa"
                            >
                                File Excel
                            </label>


                            <div class="col-sm-10">

                                <input
                                    type="file"
                                    name="file"
                                    id="file_excel_siswa"
                                    class="form-control"
                                    accept=".xls,.xlsx"
                                    required
                                >

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
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="fa fa-upload"></i>

                                    Upload Data Siswa
                                </button>

                            </div>

                        </div>


                        <div class="alert alert-warning">

                            <b>PERHATIAN:</b>

                            Gunakan format Excel yang telah
                            disediakan.

                            Jangan menambah, menghapus,
                            atau memindahkan kolom.

                            NIS dan email harus unik.

                        </div>


                        <div class="alert alert-info">

                            Format kolom:

                            <strong>
                                ID Kelas | Nama | NIS |
                                JK | Email | Password
                            </strong>

                        </div>

                    </form>

                </div>

            </div>

            <div
                class="collapse"
                id="uploadexcelcalonsiswa"
                style="margin:15px 0 0 0;"
            >

                <div class="well">

                    <form
                        action="{{
                            route(
                                'guru.siswa.candidate.import'
                            )
                        }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal"
                    >

                        @csrf


                        <div class="form-group">

                            <label
                                class="col-sm-2 control-label"
                                for="file_excel_calon"
                            >
                                File Excel Calon
                            </label>


                            <div class="col-sm-10">

                                <input
                                    type="file"
                                    name="filecalon"
                                    id="file_excel_calon"
                                    class="form-control"
                                    accept=".xls,.xlsx"
                                    required
                                >

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
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="fa fa-upload"></i>

                                    Upload Calon Siswa
                                </button>

                            </div>

                        </div>


                        <div class="alert alert-warning">

                            <b>PERHATIAN:</b>

                            Gunakan template calon siswa
                            yang telah disediakan.

                            ID Pendaftaran dan email
                            harus unik.

                        </div>


                        <div class="alert alert-info">

                            Format kolom:

                            <strong>
                                ID Kelas | Nama |
                                ID Pendaftaran |
                                JK | Sekolah Asal |
                                Email | Password
                            </strong>

                        </div>

                    </form>

                </div>

            </div>

            <hr>


            <div
                class="form-horizontal"
                style="margin-bottom:15px;"
            >

                <input
                    type="text"
                    class="form-control"
                    id="q"
                    placeholder="
                        Cari berdasarkan Nama
                        (Ketik lalu Enter)
                    "
                >

            </div>


            <img
                src="{{
                    asset(
                        'assets/assets/images/facebook.gif'
                    )
                }}"
                alt="Loading"
                id="loading_cari"
                style="display:none;"
            >


            <div
                id="wrap-user"
                class="table-responsive"
            >

                <p>
                    Jumlah siswa:
                    <strong>{{ $jumlahSiswa }}</strong>

                    &nbsp;|&nbsp;

                    Calon siswa:
                    <strong>{{ $jumlahCalonSiswa }}</strong>
                </p>


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
                                {{
                                    $users->firstItem()
                                    + $loop->index
                                }}
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
                                {{
                                    $dataUser->nama_kelas
                                    ?: '-'
                                }}
                            </td>

                            <td>

                                <a
                                    href="{{
                                        route(
                                            'guru.siswa.detail',
                                            $dataUser->id
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


                {{
                    $users->links(
                        'vendor.pagination.bootstrap-3'
                    )
                }}

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

$(document).ready(function () {

    'use strict';


    $('#q').on('keyup', function (event) {

        if (event.key !== 'Enter') {
            return;
        }

        $('#loading_cari').show();

        $.ajax({

            type: 'POST',

            url:
                '{{ route('guru.siswa.search') }}',

            data: {
                q: $('#q').val()
            },

            success: function (data) {

                $('#loading_cari').hide();

                $('#wrap-user')
                    .hide()
                    .html(data)
                    .fadeIn(300);

            },

            error: function () {

                $('#loading_cari').hide();

                alert(
                    'Gagal mencari data siswa.'
                );

            }

        });

    });


    $('#form-tambah-siswa').on(
        'submit',
        function (event) {

            event.preventDefault();

            $('#loading').show();
            $('#btnsimpansiswa').hide();

            $('#salah').hide();
            $('#benar').hide();


            $.ajax({

                type: 'POST',

                url:
                    '{{ route('guru.siswa.store') }}',

                data: {

                    nama:
                        $('#nama').val(),

                    no_induk:
                        $('#no_induk').val(),

                    email:
                        $('#email').val(),

                    jk:
                        $('#jk').val(),

                    id_kelas:
                        $('#id_kelas').val()

                },

                success: function (data) {

                    $('#loading').hide();

                    $('#btnsimpansiswa').show();


                    if (data === 'berhasil') {

                        $('#benar').show();

                        setTimeout(
                            function () {

                                window.location.reload();

                            },
                            700
                        );

                    }

                },

                error: function (xhr) {

                    $('#loading').hide();

                    $('#btnsimpansiswa').show();


                    let message =
                        'Gagal menyimpan data siswa.';


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

});

</script>

@endpush
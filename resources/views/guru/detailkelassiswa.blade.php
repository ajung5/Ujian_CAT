@extends('layouts.guru_baru')

@section('title', 'Detail Siswa')

@push('styles')
    <style>
        #image_preview {
            width: 100%;
            text-align: center;
            background: #fff;
            padding: 15px 0;
        }

        .inputfile {
            width: .1px;
            height: .1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .inputfile+label {
            font-size: 1.1em;
            font-weight: 700;
            color: #fff;
            background: #444443;
            display: inline-block;
            cursor: pointer;
            padding: 10px;
        }

        #message {
            margin-top: 15px;
        }
    </style>
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
                <a href="{{ route('guru.siswa') }}">
                    Siswa
                </a>
            </li>

            <li class="active">
                Detail Siswa
            </li>
        </ol>

        <div class="panel panel-default">
            <div class="panel-heading" style="
                background:#072047;
                color:#fff;
            ">
                Detail Siswa
            </div>

            <div class="panel-body">
                <button id="hapus" type="button" class="btn btn-danger">
                    {{ $siswa->status === 'C' ? 'Hapus Calon Siswa' : 'Hapus Siswa' }}
                </button>

                @if ($siswa->status === 'C')
                    <button type="button" class="btn btn-success" data-toggle="collapse" data-target="#wrapterimacalon">
                        <i class="fa fa-check"></i>

                        Terima Menjadi Siswa
                    </button>
                @endif

                <hr>
                <div class="row">
                    <div
                        class="
                        col-md-3
                        col-lg-3
                        text-center
                    ">
                        <img alt="{{ $siswa->nama }}"
                            src="{{ !empty($siswa->gambar) ? asset('img/' . $siswa->gambar) : asset('img/noimage.jpg') }}"
                            width="150"
                            class="
                            img-rounded
                            img-responsive
                        ">

                        <br>
                        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#wrapubahfoto">
                            Ubah Foto
                        </button>
                    </div>

                    <div class="col-md-9 col-lg-9">
                        <div class="collapse" id="wrapubahfoto" style="margin:15px 0;">
                            <div class="well">
                                <form id="uploadimagesiswa"
                                    enctype="
                                    multipart/form-data
                                ">
                                    @csrf

                                    <div id="image_preview">
                                        <img id="previewing" src="{{ asset('img/noimage.jpg') }}"
                                            style="
                                            max-width:250px;
                                            max-height:230px;
                                        ">
                                    </div>

                                    <hr>
                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                                    <input type="file" name="file" id="file" class="inputfile"
                                        accept="
                                        image/jpeg,
                                        image/png
                                    "
                                        required>

                                    <label for="file">
                                        <i class="fa fa-cloud-upload"></i>

                                        Choose a file
                                    </label>

                                    <button type="submit" class="btn btn-primary">
                                        Upload
                                    </button>
                                </form>

                                <div id="message" class="alert" style="display:none;"></div>
                            </div>
                        </div>

                        <table
                            class="
                            table
                            table-user-information
                        ">
                            <tbody>
                                <tr>
                                    <td width="140">
                                        Nama:
                                    </td>

                                    <td>
                                        {{ $siswa->nama }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>Status:</td>

                                    <td>
                                        @if ($siswa->status === 'C')
                                            <span class="label label-warning">
                                                Calon Siswa
                                            </span>
                                        @else
                                            <span class="label label-success">
                                                Siswa
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        {{ $siswa->status === 'C' ? 'ID Pendaftaran:' : 'NIS:' }}
                                    </td>

                                    <td>
                                        {{ $siswa->no_induk }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>Kelas:</td>

                                    <td>
                                        {{ $siswa->nama_kelas ?: '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>Jenis Kelamin:</td>

                                    <td>
                                        @if ($siswa->jk === 'L')
                                            Laki-laki
                                        @elseif ($siswa->jk === 'P')
                                            Perempuan
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>Email:</td>

                                    <td>
                                        {{ $siswa->email }}
                                    </td>
                                </tr>

                                @if ($siswa->status === 'C')
                                    <tr>
                                        <td>Sekolah Asal:</td>

                                        <td>
                                            {{ $siswa->sekolah_asal ?: '-' }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        @if ($siswa->status === 'C')

                            <div class="collapse" id="wrapterimacalon" style="margin:15px 0;">
                                <div class="well">
                                    <h4>
                                        Terima Calon Siswa
                                    </h4>

                                    <div class="alert alert-warning">
                                        Proses ini akan mengubah status
                                        <b>Calon Siswa</b> menjadi
                                        <b>Siswa</b> pada akun yang sama.

                                        ID Pendaftaran akan diganti dengan
                                        NIS final dan kelas tujuan yang dipilih.

                                        Email, password, foto, sekolah asal,
                                        serta histori yang sudah ada tetap
                                        dipertahankan.
                                    </div>

                                    <form id="formterimacalon" class="form-horizontal">
                                        @csrf

                                        <div class="form-group">
                                            <label
                                                class="
                                                col-sm-3
                                                control-label
                                            "
                                                for="terima_nis">
                                                NIS Final
                                            </label>

                                            <div class="col-sm-9">
                                                <input type="text" id="terima_nis" class="form-control" maxlength="50"
                                                    placeholder="Masukkan NIS final" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                class="
                                                col-sm-3
                                                control-label
                                            "
                                                for="terima_id_kelas">
                                                Kelas
                                            </label>

                                            <div class="col-sm-9">
                                                <select id="terima_id_kelas" class="form-control" required>
                                                    <option value="">
                                                        -- Pilih Kelas --
                                                    </option>

                                                    @foreach ($kelas as $dataKelas)
                                                        <option value="{{ $dataKelas->id }}" @selected((string) $siswa->id_kelas === (string) $dataKelas->id)>
                                                            {{ $dataKelas->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div
                                                class="
                                                col-sm-offset-3
                                                col-sm-9
                                            ">
                                                <button type="submit" id="btnterimacalon"
                                                    class="
                                                    btn
                                                    btn-success
                                                ">
                                                    <i class="fa fa-check"></i>

                                                    Terima Menjadi Siswa
                                                </button>

                                                <img src="{{ asset('img/ajax-loader.gif') }}" id="loaderterimacalon"
                                                    alt="Loading" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="
                                            alert
                                            alert-success
                                        "
                                            id="terimabenar" style="display:none;">
                                            Calon siswa berhasil diterima
                                            menjadi siswa.
                                        </div>

                                        <div class="
                                            alert
                                            alert-danger
                                        "
                                            id="terimasalah" style="display:none;"></div>
                                    </form>
                                </div>
                            </div>

                        @endif

                        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#wrapubah">
                            Ubah Profil
                        </button>

                        <div class="collapse" id="wrapubah" style="margin:15px 0;">
                            <div class="well">
                                <form id="formupdate" class="form-horizontal">
                                    @csrf

                                    <div class="form-group">
                                        <label
                                            class="
                                            col-sm-2
                                            control-label
                                        ">
                                            Nama
                                        </label>

                                        <div class="col-sm-10">
                                            <input type="text" id="nama" class="form-control"
                                                value="{{ $siswa->nama }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="
                                            col-sm-2
                                            control-label
                                        ">
                                            {{ $siswa->status === 'C' ? 'ID Pendaftaran' : 'NIS' }}
                                        </label>

                                        <div class="col-sm-10">
                                            <input type="text" id="nis" class="form-control"
                                                value="{{ $siswa->no_induk }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="
                                            col-sm-2
                                            control-label
                                        ">
                                            Jenis Kelamin
                                        </label>

                                        <div class="col-sm-10">
                                            <select id="jk" class="form-control">
                                                <option value="L" @selected($siswa->jk === 'L')>
                                                    Laki-laki
                                                </option>

                                                <option value="P" @selected($siswa->jk === 'P')>
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
                                        ">
                                            Email
                                        </label>

                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" value="{{ $siswa->email }}"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="
                                            col-sm-2
                                            control-label
                                        ">
                                            Password
                                        </label>

                                        <div class="col-sm-10">
                                            <input type="password" id="password" class="form-control"
                                                placeholder="
                                                Kosongkan jika
                                                tidak diubah
                                            ">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div
                                            class="
                                            col-sm-offset-2
                                            col-sm-10
                                        ">
                                            <button type="button" id="btnupdatesiswa" class="btn btn-primary">
                                                Ubah
                                            </button>
                                        </div>
                                    </div>

                                    <img src="{{ asset('img/ajax-loader.gif') }}" id="loaderupdate" alt="Loading"
                                        style="display:none;">

                                    <div class="alert alert-success" id="updatebenar" style="display:none;">
                                        Data profil berhasil diupdate.
                                    </div>

                                    <div class="alert alert-danger" id="updatesalah" style="display:none;"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12"
                        style="
                        margin-top:15px;
                        overflow-x:auto;
                    ">
                        <hr>
                        <table
                            class="
                            table
                            table-bordered
                            table-condensed
                        ">
                            <caption>
                                Daftar Ujian
                                {{ $siswa->nama }}
                            </caption>

                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Paket</th>
                                    <th>KKM</th>
                                    <th>Durasi</th>
                                    <th>Tanggal Ujian</th>
                                    <th>Score</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($ujians as $ujian)

                                    <tr>
                                        <td>
                                            {{ $ujians->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            {{ $ujian->paket }}
                                        </td>

                                        <td>
                                            {{ $ujian->kkm }}
                                        </td>

                                        <td>
                                            {{ round(((int) $ujian->waktu) / 60, 0) }}
                                            menit
                                        </td>

                                        <td>
                                            {{ $ujian->updated_at ? \Illuminate\Support\Carbon::parse($ujian->updated_at)->format('d M Y | H:i:s') : '-' }}
                                        </td>

                                        <td class="text-center">
                                            <span
                                                style="
                                            font-weight:bold;
                                            color:{{ (float) $ujian->nilai >= (float) $ujian->kkm ? '#2db300' : '#cc0000' }};
                                        ">
                                                {{ $ujian->nilai ?? 0 }}
                                            </span>
                                        </td>

                                        <td>
                                            <a href="#" class="toggle-detail"
                                                data-target="
                                            #detailjawab{{ $ujian->id }}
                                        ">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>

                                    <tr id="detailjawab{{ $ujian->id }}" style="display:none;">
                                        <td colspan="7">
                                            <div class="well"
                                                style="
                                            background:#5c6c84;
                                        ">
                                                <table class="table">
                                                    <caption
                                                        style="
                                                    color:#fff;
                                                ">
                                                        Rincian soal paket
                                                        <b>
                                                            {{ $ujian->paket }}
                                                        </b>
                                                    </caption>

                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>Soal</th>
                                                            <th>Kunci</th>
                                                            <th>Jawab</th>
                                                            <th>Score</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @forelse ($detailJawaban ->get( (string)$ujian->id_soal, collect()) as $detail)
                                                            <tr>
                                                                <td>
                                                                    {{ $loop->iteration }}
                                                                </td>

                                                                <td>
                                                                    {!! $detail->soal !!}
                                                                </td>

                                                                <td class="text-center">
                                                                    {{ $detail->kunci }}
                                                                </td>

                                                                <td class="text-center">
                                                                    {{ $detail->pilihan }}
                                                                </td>

                                                                <td class="text-center">
                                                                    {{ $detail->score }}
                                                                </td>
                                                            </tr>

                                                        @empty

                                                            <tr>
                                                                <td colspan="5">
                                                                    Detail jawaban
                                                                    tidak tersedia.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7">
                                            <div
                                                class="
                                            alert
                                            alert-danger
                                        ">
                                                <b>
                                                    {{ $siswa->nama }}
                                                </b>

                                                belum pernah mengerjakan
                                                soal ujian.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{ $ujians->links('vendor.pagination.bootstrap-3') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4 col-lg-4 dash-right">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    Aktifitas Terkini
                </h4>
            </div>

            <div class="panel-body">
                <ul class="media-list user-list">
                    @forelse ($aktifitas as $data)
                        @php
                            $gambarAktifitas = !empty($data->gambar) ? $data->gambar : 'noimage.jpg';
                        @endphp

                        <li class="media">
                            <div class="media-left">
                                <img class="
                                    media-object
                                    img-thumbnail
                                "
                                    src="{{ asset('img/' . $gambarAktifitas) }}" alt="{{ $data->nama_user }}">
                            </div>

                            <div class="media-body">
                                <h4 class="media-heading nomargin">
                                    {{ $data->nama_user }}
                                </h4>

                                {{ $data->nama }}

                                <small class="date">
                                    <i class="fa fa-clock-o"></i>

                                    {{ $data->created_at ? \Illuminate\Support\Carbon::parse($data->created_at)->format('d M Y') : '-' }}
                                </small>
                            </div>
                        </li>

                    @empty

                        <li class="media">
                            Belum ada aktivitas.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            'use strict';

            const idSiswa = {{ $siswa->id }};


            $('.toggle-detail').on(
                'click',
                function(event) {

                    event.preventDefault();

                    $(
                        $(this).data('target')
                    ).toggle();

                }
            );


            $('#btnupdatesiswa').on(
                'click',
                function() {

                    $('#loaderupdate').show();

                    $('#updatebenar').hide();
                    $('#updatesalah').hide();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.siswa.update') }}',

                        data: {

                            id_siswa: idSiswa,

                            nama: $('#nama').val(),

                            nis: $('#nis').val(),

                            jk: $('#jk').val(),

                            password: $('#password').val()

                        },

                        success: function(data) {

                            $('#loaderupdate').hide();


                            if (data === 'berhasil') {

                                $('#updatebenar').show();

                                setTimeout(
                                    function() {
                                        window.location.reload();
                                    },
                                    500
                                );

                            }

                        },

                        error: function(xhr) {

                            $('#loaderupdate').hide();


                            let message =
                                'Gagal memperbarui data siswa.';


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
                                xhr.responseText
                            ) {

                                message =
                                    xhr.responseText;

                            }


                            $('#updatesalah')
                                .html(message)
                                .show();

                        }

                    });

                }
            );



            @if ($siswa->status === 'C')

                $('#formterimacalon').on(
                    'submit',
                    function(event) {

                        event.preventDefault();


                        if (
                            !confirm(
                                'Terima calon siswa ini menjadi siswa? ' +
                                'Pastikan NIS dan kelas tujuan sudah benar.'
                            )
                        ) {
                            return;
                        }


                        $('#loaderterimacalon').show();

                        $('#btnterimacalon')
                            .prop('disabled', true);

                        $('#terimabenar').hide();
                        $('#terimasalah').hide();


                        $.ajax({

                            type: 'POST',

                            url: '{{ route('guru.siswa.candidate.accept') }}',

                            data: {

                                _token: '{{ csrf_token() }}',

                                id_siswa: idSiswa,

                                nis: $('#terima_nis').val(),

                                id_kelas: $('#terima_id_kelas').val()

                            },

                            success: function(data) {

                                $('#loaderterimacalon').hide();

                                $('#btnterimacalon')
                                    .prop('disabled', false);


                                if (data === 'berhasil') {

                                    $('#terimabenar').show();


                                    setTimeout(
                                        function() {
                                            window.location.reload();
                                        },
                                        600
                                    );

                                }

                            },

                            error: function(xhr) {

                                $('#loaderterimacalon').hide();

                                $('#btnterimacalon')
                                    .prop('disabled', false);


                                let message =
                                    'Gagal menerima calon siswa.';


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


                                $('#terimasalah')
                                    .html(message)
                                    .show();

                            }

                        });

                    }
                );
            @endif


            $('#hapus').on(
                'click',
                function() {

                    if (
                        !confirm(
                            'Yakin data siswa akan dihapus? ' +
                            'Jawaban dan data ujian siswa juga akan dihapus.'
                        )
                    ) {
                        return;
                    }


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.siswa.destroy') }}',

                        data: {
                            id_siswa: idSiswa
                        },

                        success: function(data) {

                            if (data === 'berhasil') {

                                window.location.href =
                                    '{{ route('guru.siswa') }}';

                            }

                        },

                        error: function() {

                            alert(
                                'Gagal menghapus siswa.'
                            );

                        }

                    });

                }
            );


            $('#uploadimagesiswa').on(
                'submit',
                function(event) {

                    event.preventDefault();

                    const formData =
                        new FormData(this);


                    $('#message')
                        .hide()
                        .empty();


                    $.ajax({

                        url: '{{ route('guru.siswa.photo') }}',

                        type: 'POST',

                        data: formData,

                        contentType: false,

                        processData: false,

                        success: function(data) {

                            $('#message')
                                .removeClass(
                                    'alert-danger'
                                )
                                .addClass(
                                    'alert-success'
                                )
                                .html(data)
                                .show();


                            setTimeout(
                                function() {
                                    window.location.reload();
                                },
                                500
                            );

                        },

                        error: function(xhr) {

                            let message =
                                'Upload foto gagal.';


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


                            $('#message')
                                .removeClass(
                                    'alert-success'
                                )
                                .addClass(
                                    'alert-danger'
                                )
                                .html(message)
                                .show();

                        }

                    });

                }
            );


            $('#file').on(
                'change',
                function() {

                    if (
                        !this.files ||
                        !this.files[0]
                    ) {
                        return;
                    }

                    const reader =
                        new FileReader();


                    reader.onload = function(event) {

                        $('#previewing')
                            .attr(
                                'src',
                                event.target.result
                            );

                    };


                    reader.readAsDataURL(
                        this.files[0]
                    );

                }
            );

        });
    </script>
@endpush

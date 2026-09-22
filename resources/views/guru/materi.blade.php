@extends('layouts.guru_baru')

@section('title', 'Materi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('lib/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/summernote/summernote.css') }}">
@endpush

@section('content')

    <div class="col-sm-12 col-md-8 col-lg-8 dash-left">
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('guru.index') }}">
                    Home
                </a>
            </li>

            <li class="active">
                Materi
            </li>
        </ol>

        <div class="panel panel-announcement">
            <div class="panel-body">
                <h2>Materi.</h2>

                <h4>
                    Anda dapat menulis sebuah materi yang
                    dapat diakses oleh seluruh siswa yang
                    memiliki akses untuk menggunakan aplikasi ini.
                    Materi juga bisa disertai dengan soal-soal latihan.
                </h4>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"
                style="
                background:#072047;
                color:#fff;
            ">
                Data Materi Anda.
            </div>

            <div class="panel-body">
                <button type="button" class="btn btn-primary btn-md" id="btn-materi">
                    Tulis Materi
                </button>

                <div class="well" id="wrap-materi"
                    style="
                    margin-top:15px;
                    display:none;
                    background:#fff;
                ">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="judul">
                                Judul
                            </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="judul" id="judul"
                                    placeholder="Judul">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="isi">
                                Isi
                            </label>

                            <div class="col-sm-10">
                                <textarea class="form-control" name="isi" id="isi" placeholder="Isi"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Gambar
                            </label>

                            <div class="col-sm-10">
                                <form action="{{ route('guru.materi.image') }}" method="POST" enctype="multipart/form-data"
                                    class="dropzone" id="materi-dropzone">
                                    @csrf

                                    <input type="hidden" name="sesi" id="sesi" value="{{ $sesiBaru }}">
                                    <div class="fallback">
                                        <input name="file" type="file"
                                            accept="
                                            image/jpeg,
                                            image/png,
                                            image/webp
                                        ">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Status
                            </label>

                            <div class="col-sm-10">
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="N" checked>
                                    Tidak tampil
                                </label>

                                <label class="radio-inline">
                                    <input type="radio" name="status" value="Y">
                                    Tampil
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div
                                class="
                                col-sm-offset-2
                                col-sm-10
                            ">
                                <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="Loading" id="loading"
                                    style="display:none;">

                                <div id="notif" style="display:none;"></div>

                                <button type="button" id="simpan" class="btn btn-success btn-sm">
                                    Simpan
                                </button>

                                <button type="button" id="batal" class="btn btn-danger btn-sm">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-horizontal" style="margin:15px 0;">
                    <input type="text" class="form-control" id="q"
                        placeholder="
                        Cari berdasarkan Judul
                        (Ketik lalu Enter)
                    ">
                </div>

                <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="Loading" id="loading_cari"
                    style="display:none;">

                <div class="table-responsive" id="wrap-table-materi">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Status</th>

                                <th class="text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($materis as $materi)
                                <tr id="baris{{ $materi->id }}">
                                    <td style="width:30px;">
                                        {{ $materis->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $materi->judul }}
                                    </td>

                                    <td style="width:100px;">
                                        @if ($materi->status === 'Y')
                                            <span
                                                class="
                                            label
                                            label-primary
                                        ">
                                                Tampil
                                            </span>
                                        @else
                                            <span
                                                class="
                                            label
                                            label-danger
                                        ">
                                                Tidak tampil
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center" style="width:130px;">
                                        <a href="{{ route('guru.materi.edit', $materi->id) }}"
                                            class="
                                        btn
                                        btn-primary
                                        btn-xs
                                    "
                                            title="Ubah materi">
                                            <i
                                                class="
                                            fa
                                            fa-pencil-square-o
                                        "></i>
                                        </a>

                                        <button type="button"
                                            class="
                                        btn
                                        btn-danger
                                        btn-xs
                                        btn-hapus-materi
                                    "
                                            data-id="{{ $materi->id }}" title="Hapus materi">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        <a href="{{ route('guru.materi.detail', $materi->id) }}"
                                            class="
                                        btn
                                        btn-success
                                        btn-xs
                                    "
                                            title="Detail materi">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="alert alert-danger">
                                        Belum ada data untuk ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $materis->links('vendor.pagination.bootstrap-3') }}
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

                <a href="{{ url('/aktifitas') }}" class="btn btn-success"
                    style="
                    display:block;
                    width:100%;
                    margin-top:10px;
                ">
                    Selengkapnya
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('lib/dropzone/dropzone.js') }}"></script>

    <script src="{{ asset('lib/summernote/summernote.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;


        $(document).ready(function() {

            'use strict';


            $('#isi').summernote({
                height: 150
            });


            new Dropzone(
                '#materi-dropzone', {
                    paramName: 'file',

                    maxFiles: 1,

                    maxFilesize: 5,

                    acceptedFiles: 'image/jpeg,image/png,image/webp',

                    addRemoveLinks: true,

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    }
                }
            );


            $('#btn-materi').on(
                'click',
                function() {

                    $('#wrap-materi')
                        .slideToggle();

                }
            );


            $('#batal').on(
                'click',
                function() {

                    $('#wrap-materi')
                        .slideUp();

                }
            );


            $('#q').on(
                'keyup',
                function(event) {

                    if (event.key !== 'Enter') {
                        return;
                    }

                    $('#wrap-table-materi').hide();
                    $('#loading_cari').show();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.materi.search') }}',

                        data: {
                            q: $('#q').val()
                        },

                        success: function(data) {

                            $('#loading_cari').hide();

                            $('#wrap-table-materi')
                                .html(data)
                                .fadeIn(300);

                        },

                        error: function() {

                            $('#loading_cari').hide();

                            $('#wrap-table-materi')
                                .show();

                            alert(
                                'Gagal mencari materi.'
                            );

                        }

                    });

                }
            );


            $('#simpan').on(
                'click',
                function() {

                    $('#loading').show();
                    $('#notif').hide();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.materi.save') }}',

                        data: {

                            sesi: $('#sesi').val(),

                            judul: $('#judul').val(),

                            isi: $('#isi').code(),

                            status: $(
                                'input[name="status"]:checked'
                            ).val()

                        },

                        success: function(data) {

                            $('#loading').hide();


                            if (data === 'ok') {

                                $('#notif')
                                    .removeClass(
                                        'alert-danger'
                                    )
                                    .addClass(
                                        'alert alert-info'
                                    )
                                    .html(
                                        'Materi berhasil disimpan.'
                                    )
                                    .show();


                                window.location.href =
                                    '{{ route('guru.materi') }}';

                            }

                        },

                        error: function(xhr) {

                            $('#loading').hide();


                            let message =
                                'Gagal menyimpan materi.';


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


            $(document).on(
                'click',
                '.btn-hapus-materi',
                function() {

                    const id =
                        $(this).data('id');


                    if (
                        !confirm(
                            'Yakin data materi akan dihapus?'
                        )
                    ) {
                        return;
                    }


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.materi.destroy') }}',

                        data: {
                            id: id
                        },

                        success: function(data) {

                            if (data === 'berhasil') {

                                $('#baris' + id)
                                    .fadeOut(250);

                            }

                        },

                        error: function() {

                            alert(
                                'Gagal menghapus materi.'
                            );

                        }

                    });

                }
            );

        });
    </script>
@endpush

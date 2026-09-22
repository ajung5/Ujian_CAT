@extends('layouts.guru_baru')

@section('title', 'Kelas')

@section('content')

    <div class="col-sm-12 col-md-8 col-lg-8 dash-left">
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('guru.index') }}">
                    Home
                </a>
            </li>

            <li class="active">
                Kelas
            </li>
        </ol>

        <div class="panel panel-default">
            <div class="panel-heading" style="
                background:#072047;
                color:#fff;
            ">
                Data Kelas
            </div>

            <div class="panel-body">
                <div class="alert alert-warning">
                    <span class="fa fa-exclamation-circle"></span>

                    <b>PERHATIAN:</b>

                    Merubah atau menghapus data kelas dapat
                    berdampak terhadap siswa yang terdapat di dalamnya.
                </div>

                <a href="#" class="btn btn-primary" data-toggle="collapse" id="btnwrap" data-target="#wrapubah">
                    <i class="fa fa-pencil-square-o"></i>

                    Tambah Kelas
                </a>

                <div class="collapse" id="wrapubah" style="margin:15px 0 0 0;">
                    <div class="well">
                        <form id="form-tambah-kelas" class="form-horizontal">
                            @csrf

                            <div class="form-group">
                                <label for="nama" class="col-sm-2 control-label">
                                    Nama Kelas
                                </label>

                                <div class="col-sm-10">
                                    <input type="text" name="nama" id="nama" class="form-control"
                                        placeholder="Nama kelas">
                                </div>
                            </div>

                            <div class="form-group">
                                <div
                                    class="
                                    col-sm-offset-2
                                    col-sm-10
                                ">
                                    <button type="submit" id="btntbhkelas" class="btn btn-success">
                                        Simpan
                                    </button>

                                    <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading" id="loading"
                                        style="display:none;">
                                </div>
                            </div>

                            <div id="notif" style="display:none;"></div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table
                        class="
                        table
                        table-bordered
                        table-default
                        table-striped
                        nomargin
                    "
                        style="margin:15px 0 0 0;">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No
                                </th>

                                <th class="text-center">
                                    ID.Kelas
                                </th>

                                <th class="text-center">
                                    Nama Kelas
                                </th>

                                <th class="text-center">
                                    Jumlah Siswa
                                </th>

                                <th class="text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($kelas as $daftarkelas)
                                <tr id="baris{{ $daftarkelas->id }}">
                                    <td class="text-center" width="45">
                                        {{ $kelas->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center">
                                        {{ $daftarkelas->id }}
                                    </td>

                                    <td>
                                        <span class="nama-kelas" data-id="{{ $daftarkelas->id }}"
                                            id="wrap-nama{{ $daftarkelas->id }}" style="cursor:pointer;"
                                            title="Klik untuk mengubah nama kelas">
                                            {{ $daftarkelas->nama }}
                                        </span>

                                        <input type="text"
                                            class="
                                        form-control
                                        input-nama-kelas
                                    "
                                            data-id="{{ $daftarkelas->id }}" id="nama{{ $daftarkelas->id }}"
                                            value="{{ $daftarkelas->nama }}" style="display:none;">

                                        <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="Loading"
                                            id="loading{{ $daftarkelas->id }}" style="display:none;">
                                    </td>

                                    <td class="text-center" width="175">
                                        <b>
                                            {{ $daftarkelas->jumlah_siswa }}
                                        </b>
                                    </td>

                                    <td width="90" class="text-center">
                                        <button type="button"
                                            class="
                                        btn
                                        btn-danger
                                        btn-xs
                                        btn-hapus-kelas
                                    "
                                            data-id="{{ $daftarkelas->id }}"
                                            title="
                                        Hapus kelas akan membuat
                                        siswa di dalamnya tidak memiliki kelas.
                                    ">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        <a href="{{ route('guru.kelas.detail', $daftarkelas->id) }}"
                                            class="
                                        btn
                                        btn-primary
                                        btn-xs
                                    "
                                            title="
                                        Detail kelas berisi
                                        daftar siswa di dalamnya.
                                    ">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada data kelas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $kelas->links('vendor.pagination.bootstrap-3') }}
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
                            <div class="media-body">
                                Belum ada aktivitas.
                            </div>
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


            /*
            |--------------------------------------------------------------------------
            | TAMBAH KELAS
            |--------------------------------------------------------------------------
            */

            $('#form-tambah-kelas').on(
                'submit',
                function(event) {

                    event.preventDefault();

                    $('#loading').show();
                    $('#btntbhkelas').prop('disabled', true);
                    $('#notif').hide();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.kelas.store') }}',

                        data: {
                            nama: $('#nama').val()
                        },

                        success: function(data) {

                            $('#loading').hide();
                            $('#btntbhkelas').prop('disabled', false);

                            if (data === 'berhasil') {

                                window.location.href =
                                    '{{ route('guru.kelas') }}';

                            }

                        },

                        error: function(xhr) {

                            $('#loading').hide();
                            $('#btntbhkelas').prop('disabled', false);

                            let message =
                                'Gagal menambahkan kelas.';

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
                                .removeClass('alert-info')
                                .addClass('alert alert-danger')
                                .html(message)
                                .show();

                        }

                    });

                }
            );


            /*
            |--------------------------------------------------------------------------
            | INLINE EDIT
            |--------------------------------------------------------------------------
            */

            $('.nama-kelas').on('click', function() {

                const id = $(this).data('id');

                $('#wrap-nama' + id).hide();

                $('#nama' + id)
                    .show()
                    .focus();

            });


            $('.input-nama-kelas').on(
                'keydown',
                function(event) {

                    if (event.key !== 'Enter') {
                        return;
                    }

                    event.preventDefault();

                    const id = $(this).data('id');
                    const nama = $(this).val();


                    $('#nama' + id).hide();
                    $('#loading' + id).show();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.kelas.update-inline') }}',

                        data: {
                            id: id,
                            nama: nama
                        },

                        success: function(data) {

                            $('#loading' + id).hide();

                            $('#wrap-nama' + id)
                                .html(data)
                                .show();

                        },

                        error: function(xhr) {

                            $('#loading' + id).hide();

                            $('#nama' + id).show();

                            let message =
                                'Gagal mengubah kelas.';

                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.errors
                            ) {
                                message =
                                    Object.values(
                                        xhr.responseJSON.errors
                                    )
                                    .flat()
                                    .join('\n');
                            }

                            alert(message);

                        }

                    });

                }
            );


            /*
            |--------------------------------------------------------------------------
            | HAPUS KELAS
            |--------------------------------------------------------------------------
            */

            $('.btn-hapus-kelas').on(
                'click',
                function() {

                    const id = $(this).data('id');

                    if (
                        !confirm(
                            'Yakin data kelas akan dihapus? ' +
                            'Siswa di dalam kelas akan menjadi tanpa kelas.'
                        )
                    ) {
                        return;
                    }


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.kelas.destroy') }}',

                        data: {
                            id_kelas: id
                        },

                        success: function(data) {

                            if (data === 'berhasil') {

                                $('#baris' + id)
                                    .fadeOut(250);

                            }

                        },

                        error: function() {

                            alert(
                                'Gagal menghapus data kelas.'
                            );

                        }

                    });

                }
            );

        });
    </script>
@endpush

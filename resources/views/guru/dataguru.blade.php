@extends('layouts.guru_baru')

@section('title', 'Data Guru')

@section('content')

    <div class="col-md-12 dash-left">
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('guru.index') }}">
                    Home
                </a>
            </li>

            <li class="active">
                Data Guru
            </li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading"
                style="
                background: #072047;
                color: #fff;
            ">
                Data Guru
            </div>

            <div class="panel-body">
                @if (auth()->user()->status === 'A')
                    <a href="#wrapguru" data-toggle="collapse">
                        <button type="button" class="btn btn-primary" data-toggle="tooltip"
                            title="Tambah Guru melalui form.">
                            Tambah Guru
                        </button>
                    </a>

                    <div class="collapse" id="wrapguru" style="margin:15px 0 0 0;">
                        <div class="well">
                            <form id="form-tambah-guru" class="form-horizontal">
                                @csrf

                                <div class="form-group">
                                    <label for="nama" class="col-sm-2 control-label">
                                        Nama
                                    </label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="nama" id="nama"
                                            placeholder="Nama Lengkap Guru">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="no_induk" class="col-sm-2 control-label">
                                        NIP
                                    </label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="no_induk" id="no_induk"
                                            placeholder="NIP Guru">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">
                                        Email
                                    </label>

                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" name="email" id="email"
                                            placeholder="Email Guru">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="jk" class="col-sm-2 control-label">
                                        Jenis Kelamin
                                    </label>

                                    <div class="col-sm-10">
                                        <select name="jk" id="jk" class="form-control">
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
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary" id="btnsimpan">
                                            Simpan
                                        </button>

                                        <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading" id="loading-simpan"
                                            style="display:none;">
                                    </div>
                                </div>

                                <div class="alert alert-danger" id="salah" style="display:none;"></div>

                                <div class="alert alert-info" id="benar" style="display:none;">
                                    <b>Sukses.</b>

                                    Data guru berhasil disimpan.
                                    Guru dapat masuk ke sistem menggunakan
                                    email dan password:

                                    <strong>123456</strong>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>
                @endif

                <div class="form-horizontal" style="margin-bottom:15px;">
                    <input type="text" class="form-control" id="q"
                        placeholder="Cari berdasarkan Nama (Ketik lalu Enter)">
                </div>

                <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="Loading" id="loading-search"
                    style="display:none;">

                <div class="table-responsive" id="wrap-user">
                    <table
                        class="
                        table
                        table-bordered
                        table-default
                        table-striped
                        nomargin
                    ">
                        <thead>
                            <tr>
                                <th style="text-align:center;">
                                    #
                                </th>

                                <th>Nama</th>

                                <th>NIP</th>

                                <th>Email</th>

                                <th>J.Kelamin</th>

                                <th width="70" style="text-align:center;">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $data)
                                <tr>
                                    <td style="text-align:center;">
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $data->nama }}
                                    </td>

                                    <td>
                                        {{ $data->no_induk ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $data->email }}
                                    </td>

                                    <td>
                                        @if ($data->jk === 'L')
                                            Laki-laki
                                        @elseif ($data->jk === 'P')
                                            Perempuan
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td style="text-align:center;">
                                        <a href="{{ route('guru.detail', $data->id) }}" class="btn btn-primary btn-xs"
                                            title="Detail">
                                            Detail
                                        </a>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="alert alert-danger">
                                        Belum ada data untuk ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $users->links('vendor.pagination.bootstrap-3') }}
                </div>
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
            | SEARCH
            |--------------------------------------------------------------------------
            */

            $('#q').on('keyup', function(event) {

                if (event.key !== 'Enter') {
                    return;
                }

                $('#loading-search').show();

                $.ajax({

                    type: 'POST',

                    url: '{{ route('guru.search') }}',

                    data: {
                        q: $('#q').val()
                    },

                    success: function(data) {

                        $('#loading-search').hide();

                        $('#wrap-user')
                            .hide()
                            .html(data)
                            .fadeIn(350);

                    },

                    error: function() {

                        $('#loading-search').hide();

                        alert(
                            'Terjadi kesalahan saat mencari data Guru.'
                        );

                    }

                });

            });



            /*
            |--------------------------------------------------------------------------
            | TAMBAH GURU
            |--------------------------------------------------------------------------
            */

            $('#form-tambah-guru').on(
                'submit',
                function(event) {

                    event.preventDefault();


                    $('#btnsimpan').hide();

                    $('#loading-simpan').show();

                    $('#salah').hide();

                    $('#benar').hide();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.store') }}',

                        data: {

                            nama: $('#nama').val(),

                            no_induk: $('#no_induk').val(),

                            email: $('#email').val(),

                            jk: $('#jk').val()

                        },

                        success: function(data) {

                            $('#loading-simpan').hide();

                            $('#btnsimpan').show();


                            if (data === 'berhasil') {

                                $('#salah').hide();

                                $('#benar').show();


                                setTimeout(function() {

                                    window.location.reload();

                                }, 700);

                            }

                        },

                        error: function(xhr) {

                            $('#loading-simpan').hide();

                            $('#btnsimpan').show();

                            $('#benar').hide();


                            let message =
                                'Gagal menyimpan data Guru.';


                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.errors
                            ) {

                                const errors =
                                    xhr.responseJSON.errors;

                                message =
                                    Object.values(errors)
                                    .flat()
                                    .join('<br>');

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

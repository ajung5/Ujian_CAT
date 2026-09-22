@extends('layouts.guru_baru')

@section('title', 'Detail Guru')

@push('styles')
    <link href="{{ asset('lib/dropzone/dropzone.css') }}" rel="stylesheet">
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
                <a href="{{ route('guru.data') }}">
                    Data Guru
                </a>
            </li>

            <li class="active">
                Detail Guru
            </li>
        </ol>

        <div class="panel panel-default">
            <div class="panel-heading" style="
                background:#072047;
                color:#fff;
            ">
                Detail Guru
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        @if (auth()->user()->status === 'A')
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#wrapubah">
                                Ubah Data Guru
                            </button>

                            <form method="POST" action="{{ route('guru.destroy', $user->id) }}" style="display:inline;"
                                onsubmit="
                                return confirm(
                                    'Yakin data Guru akan dihapus?'
                                );
                            ">
                                @csrf

                                <button type="submit" class="btn btn-danger">
                                    Hapus Guru
                                </button>
                            </form>

                            <div style="height:20px;"></div>

                            <div class="collapse" id="wrapubah">
                                <div class="well">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <label for="nama"
                                                class="
                                                col-sm-2
                                                control-label
                                            ">
                                                Nama
                                            </label>

                                            <div class="col-sm-10">
                                                <input type="text" name="nama" id="nama" class="form-control"
                                                    value="{{ $user->nama }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nis"
                                                class="
                                                col-sm-2
                                                control-label
                                            ">
                                                NIP
                                            </label>

                                            <div class="col-sm-10">
                                                <input type="text" name="nis" id="nis" class="form-control"
                                                    value="{{ $user->no_induk }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="jk"
                                                class="
                                                col-sm-2
                                                control-label
                                            ">
                                                Jenis Kelamin
                                            </label>

                                            <div class="col-sm-10">
                                                <select name="jk" id="jk" class="form-control">
                                                    <option value="L" @selected($user->jk === 'L')>
                                                        Laki-laki
                                                    </option>

                                                    <option value="P" @selected($user->jk === 'P')>
                                                        Perempuan
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email"
                                                class="
                                                col-sm-2
                                                control-label
                                            ">
                                                Email
                                            </label>

                                            <div class="col-sm-10">
                                                <input type="email" name="email" id="email" class="form-control"
                                                    value="{{ $user->email }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="password"
                                                class="
                                                col-sm-2
                                                control-label
                                            ">
                                                Password
                                            </label>

                                            <div class="col-sm-10">
                                                <input type="password" class="form-control" name="password" id="password"
                                                    placeholder="
                                                    Kosongkan jika
                                                    tidak diubah
                                                ">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                class="
                                                col-sm-2
                                                control-label
                                            ">
                                                Foto
                                            </label>

                                            <div class="col-sm-10">
                                                <form action="{{ route('guru.profil.photo') }}" class="dropzone">
                                                    @csrf

                                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                                    <div class="fallback">
                                                        <input name="file" type="file">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div
                                                class="
                                                col-sm-offset-2
                                                col-sm-10
                                            ">
                                                <button type="button" id="btnupdate" class="btn btn-primary">
                                                    Simpan
                                                </button>

                                                <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="Loading"
                                                    id="loading" style="display:none;">

                                                <div id="notif" style="display:none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-sm-4 col-md-4">
                        <img src="{{ !empty($user->gambar) ? asset('img/' . $user->gambar) : asset('img/noimage.jpg') }}"
                            alt="{{ $user->nama }}"
                            class="
                            img-rounded
                            img-thumbnail
                            img-responsive
                        ">
                    </div>

                    <div class="col-sm-8 col-md-8">
                        <table class="table table-user-information">
                            <tbody>
                                <tr>
                                    <td width="140">
                                        Nama:
                                    </td>

                                    <td>
                                        {{ $user->nama }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>NIP:</td>

                                    <td>
                                        {{ $user->no_induk ?: '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>Jenis Kelamin:</td>

                                    <td>
                                        @if ($user->jk === 'L')
                                            Laki-laki
                                        @elseif ($user->jk === 'P')
                                            Perempuan
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>Email:</td>

                                    <td>
                                        {{ $user->email }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                            <div class="media-body">
                                Belum ada aktivitas.
                            </div>
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

    <script>
        $(document).ready(function() {

            'use strict';


            $('#btnupdate').on('click', function() {

                const button = $(this);

                button.hide();

                $('#loading').show();

                $('#notif').hide();


                $.ajax({

                    type: 'POST',

                    url: '{{ route('guru.profil.update') }}',

                    data: {

                        id: {{ $user->id }},

                        nama: $('#nama').val(),

                        nis: $('#nis').val(),

                        jk: $('#jk').val(),

                        email: $('#email').val(),

                        password: $('#password').val()

                    },

                    success: function(data) {

                        $('#loading').hide();

                        button.show();


                        if (data === 'berhasil') {

                            $('#notif')
                                .removeClass(
                                    'alert alert-danger'
                                )
                                .addClass(
                                    'alert alert-info'
                                )
                                .html(
                                    'Profil berhasil diupdate.'
                                )
                                .show();


                            setTimeout(function() {

                                window.location.href =
                                    '{{ route('guru.detail', $user->id) }}';

                            }, 500);

                        }

                    },

                    error: function(xhr) {

                        $('#loading').hide();

                        button.show();


                        let message =
                            xhr.responseText ||
                            'Gagal memperbarui Guru.';


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
                                'alert alert-info'
                            )
                            .addClass(
                                'alert alert-danger'
                            )
                            .html(message)
                            .show();

                    }

                });

            });

        });
    </script>
@endpush

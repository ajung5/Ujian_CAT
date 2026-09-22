@extends('layouts.guru_baru')

@section('title', 'Detail Kelas')

@section('content')

    <div class="col-md-12 dash-left">
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('guru.index') }}">
                    Home
                </a>
            </li>

            <li>
                <a href="{{ route('guru.kelas') }}">
                    Kelas
                </a>
            </li>

            <li class="active">
                {{ $kelassiswa->nama }}
            </li>
        </ol>

        <div class="panel panel-default">
            <div class="panel-heading">
                Daftar siswa kelas

                <b>
                    {{ $kelassiswa->nama }}
                </b>
            </div>

            <div class="panel-body">
                <div class="alert alert-warning">
                    <span class="fa fa-exclamation-circle"></span>

                    <b>PERHATIAN:</b>

                    Anda dapat memindahkan atau mengeluarkan siswa
                    dari kelas

                    <b>{{ $kelassiswa->nama }}</b>.
                </div>

                <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#wrapubah">
                    <i class="fa fa-pencil-square-o"></i>

                    Tambah Siswa
                </button>

                <div class="collapse" id="wrapubah" style="margin:15px 0 0 0;">
                    <div class="well">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label for="siswa" class="col-sm-2 control-label">
                                    Nama Siswa
                                </label>

                                <div class="col-sm-10">
                                    <input type="hidden" id="id_kelas" value="{{ $kelassiswa->id }}">
                                    <select class="form-control" id="siswa" style="width:100%;">
                                        <option value="">
                                            -- Pilih Siswa --
                                        </option>

                                        @foreach ($calonsiswas as $calonsiswa)
                                            <option value="{{ $calonsiswa->id }}">
                                                {{ $calonsiswa->no_induk . ' - ' . $calonsiswa->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="wrapinfo" style="display:none;">
                                <div class="
                                    col-sm-offset-2
                                    col-sm-10
                                    alert
                                    alert-success
                                "
                                    id="info"></div>
                            </div>

                            <div class="form-group">
                                <div
                                    class="
                                    col-sm-offset-2
                                    col-sm-10
                                ">
                                    <button type="button" id="btntbhsiswa" class="btn btn-primary">
                                        Simpan
                                    </button>

                                    <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading" id="loaderupdate"
                                        style="display:none;">
                                </div>
                            </div>

                            <div class="
                                col-sm-offset-2
                                col-sm-10
                                alert
                                alert-success
                            "
                                id="updatebenar" style="display:none;">
                                Data siswa berhasil dipindah kelas.
                            </div>

                            <div class="
                                col-sm-offset-2
                                col-sm-10
                                alert
                                alert-danger
                            "
                                id="updatesalah" style="display:none;"></div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table
                        class="
                        table
                        table-condensed
                        table-hover
                        table-bordered
                    "
                        style="margin:15px 0 0 0;">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No
                                </th>

                                <th class="text-center">
                                    Nama
                                </th>

                                <th class="text-center">
                                    NIS
                                </th>

                                <th class="text-center">
                                    J.Kelamin
                                </th>

                                <th class="text-center">
                                    Email
                                </th>

                                <th class="text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($siswas as $siswa)
                                <tr id="baris{{ $siswa->id }}">
                                    <td width="45" class="text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $siswa->nama }}
                                    </td>

                                    <td>
                                        {{ $siswa->no_induk }}
                                    </td>

                                    <td>
                                        @if ($siswa->jk === 'L')
                                            Laki-laki
                                        @elseif ($siswa->jk === 'P')
                                            Perempuan
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        {{ $siswa->email }}
                                    </td>

                                    <td width="140" class="text-center">
                                        <button type="button"
                                            class="
                                        btn
                                        btn-danger
                                        btn-xs
                                        btn-keluarkan-siswa
                                    "
                                            data-id="{{ $siswa->id }}">
                                            Hapus
                                        </button>

                                        |

                                        <a href="{{ url('/detail-kelas-siswa/' . $siswa->id) }}">
                                            Detail
                                        </a>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="alert alert-danger">
                                            <b>Upsss:</b>

                                            Data siswa untuk kelas

                                            {{ $kelassiswa->nama }}

                                            masih kosong.
                                        </div>
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
    <script>
        $(document).ready(function() {

            'use strict';


            /*
            |--------------------------------------------------------------------------
            | CEK KELAS SISWA
            |--------------------------------------------------------------------------
            */

            $('#siswa').on('change', function() {

                const siswa = $(this).val();

                $('#wrapinfo').hide();


                if (!siswa) {
                    return;
                }


                $.ajax({

                    type: 'POST',

                    url: '{{ route('guru.kelas.siswa.check') }}',

                    data: {
                        siswa: siswa
                    },

                    success: function(data) {

                        $('#info').html(data);

                        $('#wrapinfo').fadeIn();

                    },

                    error: function(xhr) {

                        $('#info').html(
                            xhr.responseText ||
                            'Gagal memeriksa kelas siswa.'
                        );

                        $('#wrapinfo').fadeIn();

                    }

                });

            });



            /*
            |--------------------------------------------------------------------------
            | PINDAHKAN SISWA
            |--------------------------------------------------------------------------
            */

            $('#btntbhsiswa').on(
                'click',
                function() {

                    const siswa =
                        $('#siswa').val();

                    const idKelas =
                        $('#id_kelas').val();


                    $('#updatesalah').hide();
                    $('#updatebenar').hide();
                    $('#loaderupdate').show();


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.kelas.siswa.add') }}',

                        data: {

                            siswa: siswa,

                            id_kelas: idKelas

                        },

                        success: function(data) {

                            $('#loaderupdate').hide();


                            if (data === 'berhasil') {

                                $('#updatebenar').show();

                                window.location.reload();

                                return;
                            }


                            $('#updatesalah')
                                .html(data)
                                .show();

                        },

                        error: function(xhr) {

                            $('#loaderupdate').hide();


                            let message =
                                'Gagal memindahkan siswa.';


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


                            $('#updatesalah')
                                .html(message)
                                .show();

                        }

                    });

                }
            );



            /*
            |--------------------------------------------------------------------------
            | KELUARKAN SISWA DARI KELAS
            |--------------------------------------------------------------------------
            */

            $('.btn-keluarkan-siswa').on(
                'click',
                function() {

                    const id = $(this).data('id');


                    if (
                        !confirm(
                            'Siswa yang dihapus dari kelas ' +
                            'akan menjadi tidak memiliki kelas.'
                        )
                    ) {
                        return;
                    }


                    $.ajax({

                        type: 'POST',

                        url: '{{ route('guru.kelas.siswa.remove') }}',

                        data: {
                            id_siswa: id
                        },

                        success: function(data) {

                            if (data === 'berhasil') {

                                $('#baris' + id)
                                    .fadeOut(250);

                            }

                        },

                        error: function() {

                            alert(
                                'Gagal mengeluarkan siswa dari kelas.'
                            );

                        }

                    });

                }
            );

        });
    </script>
@endpush

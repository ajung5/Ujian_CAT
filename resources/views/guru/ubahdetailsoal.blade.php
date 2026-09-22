@extends('layouts.guru_baru')

@section('title', 'Ubah Detail Soal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('lib/summernote/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/dropzone/dropzone.css') }}">
@endpush

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

            <li>
                <a href="{{ route('guru.soal.detail', $soal->id) }}">
                    {{ $soal->paket }}
                </a>
            </li>

            <li class="active">
                Ubah Soal
            </li>
        </ol>

        <div class="panel panel-default">
            <div class="panel-heading"
                style="
                background:#072047;
                color:#fff;
            ">
                Ubah Daftar Soal
            </div>

            <div class="panel-body">
                <div class="form-horizontal">
                    <input type="hidden" id="id_soal" value="{{ $detailsoal->id }}">
                    <div class="form-group">
                        <label
                            class="
                            col-sm-2
                            control-label
                        ">
                            Soal
                        </label>

                        <div class="col-sm-10">
                            <textarea id="soal" class="form-control">{!! $detailsoal->soal !!}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label
                            class="
                            col-sm-2
                            control-label
                        ">
                            File Audio
                        </label>

                        <div class="col-sm-10">
                            <form action="{{ route('guru.soal.audio.upload') }}" method="POST" class="dropzone"
                                id="audio-dropzone">
                                @csrf

                                <input type="hidden" name="tampil" value="{{ $detailsoal->id }}">
                            </form>

                            @if (!empty($detailsoal->audio))
                                <div id="current-audio"
                                    style="
                                    margin-top:15px;
                                    padding:15px;
                                    border:1px solid #ddd;
                                ">
                                    <audio controls>
                                        <source src="{{ asset('assets/audios/' . $detailsoal->audio) }}">
                                    </audio>

                                    <button type="button"
                                        class="
                                        btn
                                        btn-danger
                                        btn-xs
                                    "
                                        id="hapus-audio">
                                        Hapus Audio
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    @foreach ([
            'pila' => 'Pilihan A',
            'pilb' => 'Pilihan B',
            'pilc' => 'Pilihan C',
            'pild' => 'Pilihan D',
            'pile' => 'Pilihan E',
        ] as $field => $label)
                        <div class="form-group">
                            <label
                                class="
                                col-sm-2
                                control-label
                            ">
                                {{ $label }}
                            </label>

                            <div class="col-sm-10">
                                <textarea class="form-control" id="{{ $field }}">{!! $detailsoal->{$field} !!}</textarea>
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group">
                        <label
                            class="
                            col-sm-2
                            control-label
                        ">
                            Kunci
                        </label>

                        <div class="col-sm-10">
                            <select id="kunci" class="form-control">
                                @foreach (['A', 'B', 'C', 'D', 'E'] as $jawaban)
                                    <option value="{{ $jawaban }}"
                                        {{ $detailsoal->kunci === $jawaban ? 'selected' : '' }}>
                                        {{ $jawaban }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label
                            class="
                            col-sm-2
                            control-label
                        ">
                            Score
                        </label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="score" value="{{ $detailsoal->score }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label
                            class="
                            col-sm-2
                            control-label
                        ">
                            Status
                        </label>

                        <div class="col-sm-10">
                            <select id="status" class="form-control">
                                <option value="Y" {{ $detailsoal->status === 'Y' ? 'selected' : '' }}>
                                    Tampil
                                </option>

                                <option value="N" {{ $detailsoal->status === 'N' ? 'selected' : '' }}>
                                    Tidak Tampil
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div
                            class="
                            col-sm-offset-2
                            col-sm-10
                        ">
                            <button type="button" class="btn btn-primary" id="btnubahsoal">
                                Ubah
                            </button>

                            <img src="{{ asset('img/ajax-loader.gif') }}" id="loading" style="display:none;"
                                alt="Loading">
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
                        Soal berhasil diubah.
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('lib/select2/select2.js') }}"></script>

    <script src="{{ asset('lib/summernote/summernote.js') }}"></script>

    <script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;


        $(document).ready(function() {

            $('#soal').summernote({
                height: 150
            });

            $('#pila').summernote({
                height: 100
            });

            $('#pilb').summernote({
                height: 100
            });

            $('#pilc').summernote({
                height: 100
            });

            $('#pild').summernote({
                height: 100
            });

            $('#pile').summernote({
                height: 100
            });


            $('#kunci').select2();

            $('#status').select2();


            new Dropzone(
                '#audio-dropzone', {
                    paramName: 'file',

                    maxFiles: 1,

                    maxFilesize: 10,

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')

                    },

                    success: function() {

                        window.location.reload();

                    },

                    error: function(
                        file,
                        response
                    ) {

                        console.error(
                            response
                        );

                    }

                }
            );


            $('#btnubahsoal')
                .on(
                    'click',
                    function() {

                        const button =
                            $(this);

                        button.hide();

                        $('#loading').show();


                        $.ajax({

                            type: 'POST',

                            url: '{{ route('guru.soal.detail.update') }}',

                            data: {

                                id_soal: $('#id_soal').val(),

                                soal: $('#soal').code(),

                                pila: $('#pila').code(),

                                pilb: $('#pilb').code(),

                                pilc: $('#pilc').code(),

                                pild: $('#pild').code(),

                                pile: $('#pile').code(),

                                kunci: $('#kunci').val(),

                                score: $('#score').val(),

                                status: $('#status').val()

                            },


                            success: function(
                                data
                            ) {

                                if (
                                    $.trim(data) ===
                                    'berhasil'
                                ) {

                                    window.location.href =
                                        '{{ route('guru.soal.detail', $soal->id) }}';

                                }

                            },


                            error: function(xhr) {

                                $('#loading').hide();

                                button.show();


                                let message =
                                    'Gagal mengubah soal.';


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


                                $('#salah')
                                    .html(message)
                                    .show();

                            }

                        });

                    }
                );


            $('#hapus-audio')
                .on(
                    'click',
                    function() {

                        if (
                            !confirm(
                                'Hapus audio ini?'
                            )
                        ) {
                            return;
                        }


                        $.ajax({

                            type: 'POST',

                            url: '{{ route('guru.soal.audio.destroy') }}',

                            data: {

                                id_soal: $('#id_soal').val()

                            },


                            success: function() {

                                $('#current-audio')
                                    .fadeOut();

                            }

                        });

                    }
                );

        });
    </script>
@endpush

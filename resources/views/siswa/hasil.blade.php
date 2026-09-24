@extends('layouts.siswa_baru')

@section('title', 'Hasil Ujian')

@section('breadcrumb')

    <li>
        <a href="{{ route('siswa.index') }}">
            Home
        </a>
    </li>

    <li class="active">
        Hasil Ujian
    </li>

@endsection

@section('content')

    <style>
        .result-action-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: nowrap;
            min-width: 190px;
        }

        .result-action-buttons .btn {
            min-width: 82px;
            white-space: nowrap;
        }

        @media (max-width: 767px) {
            .result-action-buttons {
                flex-wrap: wrap;
                min-width: 120px;
            }

            .result-action-buttons .btn {
                width: 100%;
            }
        }
    </style>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="media">
                    <div class="media-body">
                        <h4 class="card-title">
                            Hasil Ujian
                        </h4>
                    </div>
                </div>
            </div>

            <div style="padding:15px;">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-inline" style="margin-bottom:15px;">
                    <div class="input-group">
                        <input id="q" type="text" class="form-control" placeholder="Cari paket soal...">
                        <span class="input-group-btn">
                            <button class="btn" type="button" id="search">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <div id="loading" style="display:none;">
                    <img src="{{ asset('assets/assets/images/facebook.gif') }}" alt="loading">
                </div>

                <p class="text-muted" style="margin-bottom:10px;">
                    <i class="fa fa-info-circle"></i>
                    Review jawaban hanya tersedia untuk tipe Latihan.
                    Latihan dapat dikerjakan maksimal 3 kali.
                </p>

                <div id="wrap-hasil" style="overflow-x:auto;">
                    @include('siswa.ajax.get_hasil', [
                        'results' => $results,
                        'attemptHistory' => $attemptHistory,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            function cariHasil() {

                $('#wrap-hasil')
                    .hide();

                $('#loading')
                    .show();

                $.ajax({

                    type: 'POST',

                    url: '{{ route('siswa.results.search') }}',

                    data: {
                        q: $('#q').val()
                    },

                    success: function(data) {

                        $('#loading')
                            .hide();

                        $('#wrap-hasil')
                            .html(data)
                            .fadeIn(250);

                    },

                    error: function(xhr) {

                        $('#loading')
                            .hide();

                        $('#wrap-hasil')
                            .show();

                        alert(
                            xhr.responseJSON
                            ?.message ||
                            'Pencarian hasil gagal.'
                        );

                    }

                });

            }

            $('#q').on(
                'keyup',
                function(event) {

                    if (
                        event.key === 'Enter'
                    ) {
                        cariHasil();
                    }

                }
            );

            $('#search').on(
                'click',
                cariHasil
            );

        });
    </script>
@endpush

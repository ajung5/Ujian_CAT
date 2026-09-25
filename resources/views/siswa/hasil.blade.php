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
        .attempt-history-button,
        .result-retry-button {
            min-width: 105px;
            white-space: nowrap;
        }

        .attempt-history-button {
            border-color: #cbd5e1;
            background: #fff;
            color: #344054;
            font-weight: 600;
        }

        .attempt-history-button:hover,
        .attempt-history-button:focus {
            border-color: #98a2b3;
            background: #f8fafc;
            color: #1d2939;
        }

        .attempt-history-button .fa {
            margin-right: 4px;
        }

        .attempt-limit-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 4px;
            background: #f2f4f7;
            color: #8a949f;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .attempt-limit-status .fa {
            margin-right: 3px;
        }

        .attempt-history-modal .modal-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e4e7ec;
        }

        .attempt-history-modal .modal-title {
            margin-bottom: 5px;
            color: #344054;
            font-size: 20px;
            font-weight: 600;
        }

        .attempt-history-modal .modal-title .fa {
            margin-right: 6px;
            color: #337ab7;
        }

        .attempt-history-package {
            margin-top: 5px;
            color: #667085;
            font-size: 14px;
        }

        .attempt-history-modal .modal-body {
            padding: 20px;
        }

        .attempt-history-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 14px;
            border: 1px solid #e4e7ec;
            border-radius: 5px;
            background: #f8fafc;
            color: #667085;
        }

        .attempt-history-summary strong {
            color: #344054;
            font-size: 16px;
        }

        .attempt-history-table {
            margin-bottom: 0;
        }

        .attempt-history-table>thead>tr>th {
            vertical-align: middle;
            background: #f8fafc;
        }

        .attempt-history-table>tbody>tr>td {
            vertical-align: middle;
        }

        .attempt-review-button {
            min-width: 92px;
            white-space: nowrap;
        }

        .history-status {
            display: inline-block;
            font-weight: 600;
            white-space: nowrap;
        }

        .history-status-success {
            color: #009900;
        }

        .history-status-failed {
            color: #e60000;
        }

        @media (max-width: 767px) {

            .attempt-history-button,
            .result-retry-button {
                width: 100%;
                min-width: 95px;
            }

            .attempt-history-modal .modal-dialog {
                margin: 10px;
            }

            .attempt-history-summary {
                gap: 15px;
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

            /*
             * Modal Riwayat Percobaan harus menjadi child langsung
             * dari <body>.
             *
             * Layout AdminPlus menggunakan stacking context z-index 998,
             * sedangkan Bootstrap modal backdrop menggunakan z-index 1040.
             * Jika modal tetap berada di dalam .layout-content, backdrop
             * akan berada di atas modal dan membuat modal tidak dapat diklik.
             */
            $(document).on(
                'show.bs.modal',
                '.attempt-history-modal',
                function(event) {

                    const modal =
                        $(event.target);

                    if (
                        modal.parent()[0] !==
                        document.body
                    ) {

                        modal.appendTo(
                            document.body
                        );

                    }

                }
            );

            /*
             * Setelah modal ditutup, kembalikan ke #wrap-hasil.
             * Ini penting karena isi hasil dapat diganti melalui AJAX
             * ketika siswa melakukan pencarian.
             */
            $(document).on(
                'hidden.bs.modal',
                '.attempt-history-modal',
                function(event) {

                    const modal =
                        $(event.target);

                    const container =
                        $('#wrap-hasil');

                    if (
                        container.length &&
                        modal.parent()[0] !==
                        container[0]
                    ) {

                        modal.appendTo(
                            container
                        );

                    }

                }
            );

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

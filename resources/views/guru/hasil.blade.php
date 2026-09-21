@extends('layouts.guru_baru')

@section('title', 'Laporan')


@section('content')

<div class="col-md-12 dash-left">

    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>

        <li class="active">
            Laporan
        </li>

    </ol>


    <div class="panel panel-default">

        <div
            class="panel-heading"
            style="
                background:#072047;
                color:#fff;
            "
        >
            Laporan
        </div>


        <div class="panel-body">

            <div
                class="alert alert-info"
                role="alert"
            >

                <b>
                    <i class="fa fa-info-circle"></i>
                    Tips:
                </b>

                Di bawah ini daftar paket soal
                yang telah dikerjakan oleh siswa.

                Klik tombol Detail untuk melihat
                rekap hasil per kelas.

            </div>


            <hr class="clearfix">


            <div
                class="form-horizontal"
                style="margin-bottom:15px;"
            >

                <input
                    type="text"
                    class="form-control"
                    id="q"
                    placeholder="
                        Cari berdasarkan paket soal
                        (ketik lalu Enter)
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
                id="wrap-hasil"
                class="table-responsive"
            >

                @include(
                    'guru.ajax.get_hasil_guru',
                    [
                        'jawabs' => $jawabs,
                    ]
                )

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {

    $('#q').on(
        'keyup',
        function (event) {

            if (event.key !== 'Enter') {
                return;
            }


            $('#loading_cari')
                .show();


            $.ajax({

                type: 'POST',

                url:
                    '{{
                        route(
                            'guru.results.search'
                        )
                    }}',

                data: {
                    q: $('#q').val()
                },


                success: function (data) {

                    $('#loading_cari')
                        .hide();


                    $('#wrap-hasil')
                        .hide()
                        .html(data)
                        .fadeIn(350);

                },


                error: function (xhr) {

                    $('#loading_cari')
                        .hide();


                    alert(
                        xhr.responseJSON
                            ?.message ||
                        'Pencarian laporan gagal.'
                    );

                }

            });

        }
    );

});

</script>

@endpush
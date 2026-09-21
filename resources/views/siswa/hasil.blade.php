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


            <div
                class="form-inline"
                style="margin-bottom:15px;"
            >

                <div class="input-group">

                    <input
                        id="q"
                        type="text"
                        class="form-control"
                        placeholder="Cari paket soal..."
                    >

                    <span class="input-group-btn">

                        <button
                            class="btn"
                            type="button"
                            id="search"
                        >

                            <i class="fa fa-search"></i>

                        </button>

                    </span>

                </div>

            </div>


            <div
                id="loading"
                style="display:none;"
            >

                <img
                    src="{{
                        asset(
                            'assets/assets/images/facebook.gif'
                        )
                    }}"
                    alt="loading"
                >

            </div>


            <div
                id="wrap-hasil"
                style="overflow-x:auto;"
            >

                @include(
                    'siswa.ajax.get_hasil',
                    [
                        'results' => $results,
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

    function cariHasil() {

        $('#wrap-hasil')
            .hide();

        $('#loading')
            .show();


        $.ajax({

            type: 'POST',

            url:
                '{{
                    route(
                        'siswa.results.search'
                    )
                }}',

            data: {
                q: $('#q').val()
            },


            success: function (data) {

                $('#loading')
                    .hide();

                $('#wrap-hasil')
                    .html(data)
                    .fadeIn(250);

            },


            error: function (xhr) {

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
        function (event) {

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
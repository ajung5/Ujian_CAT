@extends('layouts.siswa_baru')

@section('title', 'Latihan')


@push('styles')

<style>

.hideGambar {
    overflow: hidden;
    height: 100px;
}

.showGambar {
    text-align: center;
    background: #d6dbd7;
    padding: 15px 0;
    cursor: pointer;
}

.showGambar:hover {
    background: #c4c4c4;
}

.training-package {
    margin-bottom: 15px;
}

</style>

@endpush


@section('breadcrumb')

<li>

    <a href="{{ route('siswa.index') }}">
        Home
    </a>

</li>


<li>

    <a href="{{ route('siswa.latihan') }}">
        Latihan
    </a>

</li>


<li class="active">

    Detail:
    {{ $materi->judul }}

</li>

@endsection


@section('content')

<div class="col-md-12">

    <h1 class="page-heading h2">

        {{ $materi->judul }}

    </h1>


    <div class="row">

        <div class="col-md-8">

            <div class="card">

                @if (
                    ! empty(
                        $materi->gambar
                    )
                )

                    <div
                        class="hideGambar"
                        id="wrap-gambar"
                    >

                        <img
                            src="{{
                                asset(
                                    'img/materi/'.
                                    basename(
                                        $materi->gambar
                                    )
                                )
                            }}"
                            alt="{{
                                $materi->judul
                            }}"
                            style="width:100%;"
                        >

                    </div>


                    <div
                        class="showGambar"
                        id="show-gambar"
                    >

                        Tampil Gambar

                    </div>

                @endif


                <div class="card-block">

                    {!!
                        $materi->isi
                    !!}


                    <hr>


                    <div class="row">

                        <div class="col-md-12">

                            <h4>
                                Soal-soal latihan
                            </h4>

                            <hr>


                            @if (
                                $soals->count()
                            )

                                <div class="row">

                                    @foreach (
                                        $soals
                                        as $soal
                                    )

                                        <div
                                            class="
                                                col-md-6
                                                training-package
                                            "
                                        >

                                            <div class="card">

                                                <div
                                                    class="
                                                        card-header
                                                        bg-white
                                                        center
                                                    "
                                                >

                                                    <h4
                                                        class="
                                                            card-title
                                                        "
                                                    >

                                                        {{
                                                            $soal
                                                                ->paket
                                                        }}

                                                    </h4>

                                                </div>


                                                <div
                                                    class="
                                                        card-block
                                                    "
                                                >

                                                    <p
                                                        class="m-b-0"
                                                        style="
                                                            color:#a6aab2;
                                                            font-size:11pt;
                                                        "
                                                    >

                                                        {{
                                                            $soal
                                                                ->deskripsi
                                                        }}

                                                    </p>


                                                    <hr>
                                                    <a
                                                        href="{{
                                                            route(
                                                                'siswa.training',
                                                                $soal->id
                                                            )
                                                        }}"
                                                        class="
                                                            btn
                                                            btn-primary
                                                        "
                                                    >

                                                        <i
                                                            class="
                                                                fa
                                                                fa-pencil
                                                            "
                                                        ></i>

                                                        Mulai Latihan

                                                    </a>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <div
                                    class="
                                        alert
                                        alert-danger
                                    "
                                    style="
                                        margin-bottom:0;
                                    "
                                >

                                    Belum ada soal
                                    latihan.

                                </div>

                            @endif

                        </div>

                    </div>


                    <hr class="clearfix">

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card">

                <div
                    class="
                        card-header
                        bg-white
                    "
                >

                    <div class="media">

                        @if (
                            $materi->user &&
                            ! empty(
                                $materi
                                    ->user
                                    ->gambar
                            )
                        )

                            <div
                                class="
                                    media-left
                                    media-middle
                                "
                            >

                                <img
                                    src="{{
                                        asset(
                                            'img/'.
                                            basename(
                                                $materi
                                                    ->user
                                                    ->gambar
                                            )
                                        )
                                    }}"
                                    alt="Pengajar"
                                    width="50"
                                    class="img-circle"
                                >

                            </div>

                        @endif


                        <div
                            class="
                                media-body
                                media-middle
                            "
                        >

                            <h4 class="card-title">

                                {{
                                    $materi->user
                                        ? $materi
                                            ->user
                                            ->nama
                                        : 'Pengajar'
                                }}

                            </h4>


                            <p class="card-subtitle">

                                @if (
                                    $materi->user &&
                                    $materi
                                        ->user
                                        ->status === 'A'
                                )

                                    Admin

                                @else

                                    Guru

                                @endif

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {

    $('#show-gambar')
        .on(
            'click',
            function () {

                $(this)
                    .hide();

                $('#wrap-gambar')
                    .removeClass(
                        'hideGambar'
                    )
                    .hide()
                    .fadeIn(350);

            }
        );

});

</script>

@endpush
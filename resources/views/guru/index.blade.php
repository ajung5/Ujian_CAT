@extends('layouts.guru_baru')


@section(
    'title',
    'Selamat datang di aplikasi ujian berbasis komputer.'
)


@section('content')

@php

    $sapaan = $user->jk === 'L'
        ? 'Pak'
        : 'Ibu';

@endphp


<div class="col-sm-12 col-md-8 col-lg-8 dash-left">


    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>

        <li class="active">
            Anda berada di halaman depan
        </li>

    </ol>


    <div class="panel panel-announcement">

        <ul class="panel-options">

            <li>
                <a class="panel-remove">
                    <i class="fa fa-remove"></i>
                </a>
            </li>

        </ul>


        <div class="panel-body">

            <h2>

                Selamat Datang

                {{ $sapaan }} {{ $user->nama }}

                di halaman Guru

                <span class="text-primary">
                    Aplikasi Ujian Berbasis Komputer
                </span>.

            </h2>


            <h4>

                Silahkan kelola seluruh menu yang ada disini.
                Setiap aktifitas Anda akan selalu tercatat oleh sistem.

            </h4>

        </div>

    </div>


    <div class="panel panel-default">


        <div
            class="panel-heading"
            style="
                background: #072047;
                color: #fff;
            "
        >

            Selamat Datang di Aplikasi Ujian Berbasis Komputer

            <b>
                {{ $school?->nama ?? 'Ujian CAT' }}
            </b>

        </div>


        <div class="panel-body">

            <div class="row">


                <div class="col-sm-4 col-md-4">

                    @if (empty($user->gambar))

                        <img
                            src="{{ asset('img/noimage.jpg') }}"
                            alt="Foto Guru"
                            class="img-rounded img-responsive"
                        >

                    @else

                        <img
                            src="{{ asset('img/' . $user->gambar) }}"
                            alt="{{ $user->nama }}"
                            class="img-rounded img-responsive"
                        >

                    @endif

                </div>


                <div class="col-sm-8 col-md-8">

                    <blockquote>

                        <p>
                            {{ $user->nama }}
                        </p>

                        <small>

                            <cite>
                                {{ $user->no_induk }}
                            </cite>

                        </small>

                    </blockquote>


                    <p>

                        <i class="fa fa-envelope-o"></i>

                        {{ $user->email }}

                        <br>


                        <i class="fa fa-venus-mars"></i>

                        @if ($user->jk === 'L')

                            Laki-laki

                        @elseif ($user->jk === 'P')

                            Perempuan

                        @else

                            -

                        @endif

                    </p>

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

                        $gambarAktifitas =
                            ! empty($data->gambar)
                                ? $data->gambar
                                : 'noimage.jpg';

                    @endphp


                    <li class="media">


                        <div class="media-left">

                            <a href="#">

                                <img
                                    class="media-object img-thumbnail"
                                    src="{{
                                        asset(
                                            'img/' .
                                            $gambarAktifitas
                                        )
                                    }}"
                                    alt="{{ $data->nama_user }}"
                                >

                            </a>

                        </div>


                        <div class="media-body">


                            <h4 class="media-heading nomargin">

                                <a href="#">

                                    {{ $data->nama_user }}

                                </a>

                            </h4>


                            {{ $data->nama }}


                            <small class="date">

                                <i class="fa fa-clock-o"></i>

                                {{
                                    $data->created_at
                                        ? $data->created_at
                                            ->format('d M Y')
                                        : '-'
                                }}

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


            <a
                href="{{ url('/aktifitas') }}"
                class="btn btn-success"
                style="
                    display: block;
                    width: 100%;
                    margin: 10px 0 0 0;
                "
            >

                Selengkapnya

            </a>


        </div>

    </div>

</div>

@endsection
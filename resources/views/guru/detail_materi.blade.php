@extends('layouts.guru_baru')

@section('title', $materi->judul)


@section('content')


<div class="col-sm-12 col-md-8 col-lg-8 dash-left">

    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>

        <li>
            <a href="{{ route('guru.materi') }}">
                Materi
            </a>
        </li>

        <li class="active">
            Detail: {{ $materi->judul }}
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
            Detail Materi
        </div>


        <div class="panel-body">

            <h3>
                {{ $materi->judul }}
            </h3>


            @if (! empty($materi->gambar))

                <p>

                    <img
                        src="{{
                            asset(
                                'img/materi/'.
                                $materi->gambar
                            )
                        }}"
                        class="
                            img
                            img-thumbnail
                            img-responsive
                        "
                        alt="{{ $materi->judul }}"
                    >

                </p>

            @endif


            <p>

                <i class="fa fa-calendar"></i>

                {{
                    $materi->created_at
                        ? $materi
                            ->created_at
                            ->format('d M Y')
                        : '-'
                }}

            </p>


            <div class="materi-content">

                {!! $materi->isi !!}

            </div>


            <hr>


            <a
                href="{{
                    route(
                        'guru.materi.edit',
                        $materi->id
                    )
                }}"
                class="btn btn-primary"
            >

                <i
                    class="
                        fa
                        fa-pencil-square-o
                    "
                ></i>

                Ubah Materi

            </a>

        </div>

    </div>

</div>


@include('guru.partials.aktivitas', [
    'aktifitas' => $aktifitas
])

@endsection
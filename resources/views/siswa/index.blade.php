@extends('layouts.siswa_baru')

@section('title', 'Selamat Datang')


@section('breadcrumb')

    <li>
        <a href="{{ route('siswa.index') }}">
            Home
        </a>
    </li>

    <li class="active">
        Anda berada di Home
    </li>

@endsection


@section('content')

<div class="col-md-12">

    <div class="card">

        <div class="card-header bg-white">

            <div class="media">

                <div class="media-body">

                    <h4 class="card-title">
                        Selamat Datang
                    </h4>

                </div>

            </div>

        </div>


        <div style="padding:15px;">

            <p>

                Hai

                <b>
                    {{ $user->nama }}
                </b>.

                Aplikasi ujian ini dirancang
                untuk memudahkan proses ujian.

                Ikutilah instruksi Guru untuk
                mengoperasikan aplikasi ini
                dengan benar.

            </p>


            @if (
                ! empty($user->nama_kelas)
            )

                <p>

                    Kelas Anda:

                    <b>
                        {{ $user->nama_kelas }}
                    </b>

                </p>

            @else

                <div class="alert alert-warning">

                    Anda belum terdaftar pada
                    kelas.

                    Hubungi Guru atau Administrator.

                </div>

            @endif


            <a
                href="{{ route('siswa.soal') }}"
                class="btn btn-primary"
            >

                <i class="fa fa-list-alt"></i>

                Lihat Soal Ujian

            </a>

        </div>

    </div>

</div>

@endsection
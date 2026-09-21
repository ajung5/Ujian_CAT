@extends('layouts.siswa_baru')

@section('title', 'Profil')


@section('breadcrumb')

<li>

    <a href="{{ route('siswa.index') }}">
        Home
    </a>

</li>

<li class="active">
    Profil
</li>

@endsection


@section('content')

@php

    $jenisKelamin =
        $user->jk === 'L'
            ? 'Laki-laki'
            : 'Perempuan';


    $foto =
        ! empty($user->gambar)
            ? $user->gambar
            : 'siswa.png';

@endphp


<div class="col-md-12">

    <div class="card">

        <div class="card-header bg-white">

            <div class="media">

                <div class="media-body">

                    <h4 class="card-title">
                        Profil
                    </h4>

                </div>

            </div>

        </div>


        <div style="padding:15px;">

            <div class="row">

                <div
                    class="
                        col-sm-3
                        col-md-3
                    "
                >

                    <img
                        src="{{
                            asset(
                                'img/'.$foto
                            )
                        }}"
                        alt="Foto {{ $user->nama }}"
                        class="
                            img-rounded
                            img-thumbnail
                        "
                        style="
                            max-width:100%;
                        "
                    >

                </div>


                <div
                    class="
                        col-sm-9
                        col-md-9
                    "
                >

                    <blockquote>

                        <h3>
                            {{ $user->nama }}
                        </h3>

                        <small>

                            <cite>
                                {{ $user->no_induk }}
                            </cite>

                        </small>

                    </blockquote>


                    <p>

                        <i
                            class="
                                fa
                                fa-envelope
                            "
                        ></i>

                        {{ $user->email }}

                        <br>


                        <i
                            class="
                                fa
                                fa-venus-mars
                            "
                        ></i>

                        {{ $jenisKelamin }}

                        <br>


                        <i
                            class="
                                fa
                                fa-drivers-license
                            "
                        ></i>

                        {{
                            $user->nama_kelas
                            ?? 'Belum memiliki kelas'
                        }}

                    </p>


                    <div class="clearfix"></div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
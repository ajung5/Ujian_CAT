@extends('layouts.siswa_baru')

@section('title', 'Soal Ujian')


@section('breadcrumb')

    <li>
        <a href="{{ route('siswa.index') }}">
            Home
        </a>
    </li>

    <li class="active">
        Soal Ujian
    </li>

@endsection


@section('content')

  @if (session('error'))

      <div class="col-md-12">

          <div class="alert alert-danger">

              {{ session('error') }}

          </div>

      </div>

  @endif


  @if (session('success'))

      <div class="col-md-12">

          <div class="alert alert-success">

              {{ session('success') }}

          </div>

      </div>

  @endif

<div class="col-md-12">

    @if (
        empty($user->id_kelas)
    )

        <div class="alert alert-warning">

            <i
                class="
                    fa
                    fa-exclamation-triangle
                "
            ></i>

            Anda belum memiliki kelas.

            Silakan hubungi Guru atau
            Administrator.

        </div>

    @elseif (
        $distribusisoal->count()
    )

        <div class="card-columns">

            @foreach (
                $distribusisoal
                as $dataSoal
            )

                <div class="card">

                    <div
                        class="
                            card-header
                            bg-white
                            center
                        "
                    >

                        <h4 class="card-title">

                            {{ $dataSoal->paket }}

                        </h4>

                    </div>


                    <div class="card-block">

                        <p
                            class="m-b-0"
                            style="
                                color:#a6aab2;
                            "
                        >

                            {{
                                $dataSoal->deskripsi
                            }}

                        </p>


                        <hr>


                        <table
                            class="
                                table
                                table-condensed
                            "
                        >

                            <tbody>

                            <tr>

                                <td>
                                    KKM
                                </td>

                                <td>
                                    :
                                </td>

                                <td>
                                    {{
                                        $dataSoal->kkm
                                    }}
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Waktu
                                </td>

                                <td>
                                    :
                                </td>

                                <td>

                                    {{
                                        number_format(
                                            ((int)
                                            $dataSoal->waktu)
                                            / 60,
                                            0
                                        )
                                    }}

                                    menit

                                </td>

                            </tr>

                            </tbody>

                        </table>
                        <a
                            href="{{
                                route(
                                    'siswa.exam',
                                    $dataSoal->id_soal
                                )
                            }}"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-pencil"></i>

                            Mulai Ujian
                        </a>
                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="alert alert-info">

            <i
                class="
                    fa
                    fa-info-circle
                "
            ></i>

            Belum ada paket soal
            untuk dikerjakan.

        </div>

    @endif

</div>

@endsection
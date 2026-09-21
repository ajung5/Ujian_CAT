@extends('layouts.guru_baru')

@section('title', 'Detail Hasil per Kelas')


@section('content')

<div class="col-md-12 dash-left">

    <ol class="breadcrumb">

        <li>
            <a href="{{ route('guru.index') }}">
                Home
            </a>
        </li>


        <li>
            <a href="{{ route('guru.results') }}">
                Laporan
            </a>
        </li>


        <li>

            <a
                href="{{
                    route(
                        'guru.results.detail',
                        $soal->id
                    )
                }}"
            >
                Detail Kelas
            </a>

        </li>


        <li class="active">

            {{
                (string) $soal->jenis === '2'
                    ? 'Latihan'
                    : 'Ujian'
            }}

            Kelas

            {{ $kelas->nama }}

        </li>

    </ol>


    <div class="panel panel-default">

        <div class="panel-heading">

            Detail

            {{
                (string) $soal->jenis === '2'
                    ? 'Latihan'
                    : 'Ujian'
            }}

            Kelas

            <b>
                {{ $kelas->nama }}
            </b>

            Paket

            <b>
                {{ $soal->paket }}
            </b>

        </div>


        <div class="panel-body">

            <div
                class="alert alert-info"
                role="alert"
            >

                <b>
                    <i class="fa fa-info-circle"></i>
                    Info:
                </b>

                Di bawah ini daftar siswa
                kelas

                <b>
                    {{ $kelas->nama }}
                </b>

                yang telah menyelesaikan paket

                <b>
                    {{ $soal->paket }}
                </b>.

            </div>


            <div class="table-responsive">

                <table
                    class="
                        table
                        table-bordered
                        table-hover
                        table-condensed
                    "
                >

                    <thead>

                    <tr>

                        <th width="40">
                            No
                        </th>

                        <th>
                            NIS
                        </th>

                        <th>
                            Nama
                        </th>

                        <th
                            width="90"
                            class="text-center"
                        >
                            Nilai
                        </th>

                        <th
                            width="100"
                            class="text-center"
                        >
                            Status
                        </th>

                        <th
                            width="160"
                            class="text-center"
                        >
                            Aksi
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse (
                        $jawabs
                        as $index => $jawab
                    )

                        @php

                            $nilai =
                                (float)
                                $jawab
                                    ->total_score;

                            $lulus =
                                $nilai >=
                                (float)
                                $soal->kkm;

                            $answers =
                                $answersByUser
                                    ->get(
                                        $jawab
                                            ->id_user,
                                        collect()
                                    );

                        @endphp


                        <tr
                            id="student-row-{{
                                $jawab->id_user
                            }}"
                            class="{{
                                $lulus
                                    ? 'success'
                                    : 'danger'
                            }}"
                        >

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>
                                {{ $jawab->no_induk }}
                            </td>


                            <td>
                                {{ $jawab->nama }}
                            </td>


                            <td class="text-center">

                                <strong>

                                    {{
                                        rtrim(
                                            rtrim(
                                                number_format(
                                                    $nilai,
                                                    2,
                                                    '.',
                                                    ''
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        )
                                    }}

                                </strong>

                            </td>


                            <td class="text-center">

                                @if ($lulus)

                                    <span
                                        class="
                                            label
                                            label-success
                                        "
                                    >
                                        Lulus
                                    </span>

                                @else

                                    <span
                                        class="
                                            label
                                            label-danger
                                        "
                                    >
                                        Tidak Lulus
                                    </span>

                                @endif

                            </td>


                            <td class="text-center">

                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-xs
                                        btn-primary
                                        btn-detail-answer
                                    "
                                    data-user-id="{{
                                        $jawab->id_user
                                    }}"
                                >

                                    <i class="fa fa-search"></i>

                                    Detail

                                </button>


                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-xs
                                        btn-danger
                                        btn-delete-student
                                    "
                                    data-user-id="{{
                                        $jawab->id_user
                                    }}"
                                    data-class-id="{{
                                        $kelas->id
                                    }}"
                                    data-exam-id="{{
                                        $soal->id
                                    }}"
                                >

                                    <i class="fa fa-trash-o"></i>

                                    Hapus

                                </button>

                            </td>

                        </tr>


                        <tr
                            id="detail-{{
                                $jawab->id_user
                            }}"
                            style="display:none;"
                        >

                            <td colspan="6">

                                <table
                                    class="
                                        table
                                        table-condensed
                                        table-bordered
                                        table-hover
                                        table-striped
                                    "
                                >

                                    <thead>

                                    <tr>

                                        <th width="40">
                                            No
                                        </th>

                                        <th>
                                            Soal
                                        </th>

                                        <th
                                            width="70"
                                            class="text-center"
                                        >
                                            Kunci
                                        </th>

                                        <th
                                            width="70"
                                            class="text-center"
                                        >
                                            Jawab
                                        </th>

                                        <th
                                            width="70"
                                            class="text-center"
                                        >
                                            Score
                                        </th>

                                    </tr>

                                    </thead>


                                    <tbody>

                                    @forelse (
                                        $answers
                                        as $answerIndex => $answer
                                    )

                                        <tr>

                                            <td>
                                                {{ $answerIndex + 1 }}
                                            </td>


                                            <td>
                                                {!! $answer->soal !!}
                                            </td>


                                            <td class="text-center">

                                                {{
                                                    strtoupper(
                                                        $answer->kunci
                                                    )
                                                }}

                                            </td>


                                            <td class="text-center">

                                                {{
                                                    $answer->pilihan
                                                    !== null &&
                                                    $answer->pilihan
                                                    !== ''
                                                        ? strtoupper(
                                                            $answer
                                                                ->pilihan
                                                        )
                                                        : '-'
                                                }}

                                            </td>


                                            <td class="text-center">

                                                {{ $answer->score }}

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="text-center"
                                            >
                                                Detail jawaban
                                                tidak tersedia.
                                            </td>

                                        </tr>

                                    @endforelse

                                    </tbody>

                                </table>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="alert alert-info"
                            >
                                Belum ada hasil siswa.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {

    $('.btn-detail-answer')
        .on(
            'click',
            function () {

                const userId =
                    $(this)
                        .data(
                            'user-id'
                        );


                $('#detail-' + userId)
                    .toggle();

            }
        );


    $('.btn-delete-student')
        .on(
            'click',
            function () {

                if (
                    ! confirm(
                        'Yakin hasil siswa ini akan dihapus?'
                    )
                ) {
                    return;
                }


                const button =
                    $(this);

                const userId =
                    button.data(
                        'user-id'
                    );

                const classId =
                    button.data(
                        'class-id'
                    );

                const examId =
                    button.data(
                        'exam-id'
                    );


                button.prop(
                    'disabled',
                    true
                );


                $.ajax({

                    type: 'POST',

                    url:
                        '{{
                            route(
                                'guru.results.student.destroy'
                            )
                        }}',

                    data: {

                        id_user:
                            userId,

                        id_kelas:
                            classId,

                        id_soal:
                            examId

                    },


                    success: function () {

                        $('#student-row-' + userId)
                            .remove();

                        $('#detail-' + userId)
                            .remove();

                    },


                    error: function (xhr) {

                        button.prop(
                            'disabled',
                            false
                        );


                        alert(
                            xhr.responseJSON
                                ?.message ||
                            'Hasil siswa gagal dihapus.'
                        );

                    }

                });

            }
        );

});

</script>

@endpush
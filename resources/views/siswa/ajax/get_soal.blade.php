@php

    $pilihan =
        $cekJawaban
            ? strtoupper(
                trim(
                    (string)
                    $cekJawaban->pilihan
                )
            )
            : '';

@endphp


<table
    class="
        table
        table-condensed
    "
    style="
        padding:0;
        margin:0;
    "
>

    <tbody>

    <tr>

        <input
            type="hidden"
            class="question-id-soal"
            value="{{ $detailsoal->id_soal }}"
        >

        <input
            type="hidden"
            class="question-detail-id"
            value="{{ $detailsoal->id }}"
        >


        <td colspan="2">

            @if (
                ! empty(
                    $detailsoal->audio
                )
            )

                <div
                    style="
                        margin:
                        0 0 20px 0;
                        padding:15px;
                        border:
                        1px solid #a8a8a8;
                    "
                >

                    <span
                        style="
                            color:#828282;
                        "
                    >
                        Audio for Listening
                    </span>

                    <hr>


                    <audio controls>

                        <source
                            src="{{
                                asset(
                                    'assets/audios/'.
                                    $detailsoal->audio
                                )
                            }}"
                        >

                        Browser Anda tidak
                        mendukung audio.

                    </audio>

                </div>

            @endif


            {!! $detailsoal->soal !!}

        </td>

    </tr>


    @foreach (
        [
            'A' => $detailsoal->pila,
            'B' => $detailsoal->pilb,
            'C' => $detailsoal->pilc,
            'D' => $detailsoal->pild,
            'E' => $detailsoal->pile,
        ]
        as $kode => $teks
    )

        <tr
            id="wrap_pil_{{
                strtolower($kode)
            }}"
            class="{{
                $pilihan === $kode
                    ? 'benar'
                    : ''
            }}"
        >

            <td style="width:10px;">

                <input
                    type="radio"
                    name="pilih{{
                        $detailsoal->id
                    }}"
                    value="{{ $kode }}"
                    {{
                        $pilihan === $kode
                            ? 'checked'
                            : ''
                    }}
                >

            </td>


            <td>
                {!! $teks !!}
            </td>

        </tr>

    @endforeach

    </tbody>

</table>
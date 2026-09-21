<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Hasil {{ $soal->paket }}
    </title>


    <link
        rel="icon"
        href="{{ asset('img/favicon.png') }}"
    >


    <link
        rel="stylesheet"
        href="{{
            asset(
                'assets_lama/bootstrap/css/bootstrap.min.css'
            )
        }}"
    >


    <style>

        html,
        body {
            min-height: 100%;
        }

        body {
            background-color: #8f6253;
        }

        #container {
            overflow: hidden;
            padding-top: 30px;
        }

        #result-wrap {
            background: #fff;
            padding: 20px;
        }

        .result-title {
            margin-top: 0;
            margin-bottom: 5px;
        }

        .result-subtitle {
            margin-bottom: 20px;
            color: #777;
        }

        .score {
            font-weight: bold;
            font-size: 16px;
        }

    </style>

</head>


<body>

<div
    id="container"
    class="container"
>

    <div id="result-wrap">

        <h3 class="result-title">

            {{ $soal->paket }}

        </h3>


        <p class="result-subtitle">

            Kelas:
            <strong>
                {{ $kelas->nama }}
            </strong>

        </p>


        <table
            class="
                table
                table-bordered
                table-striped
                table-condensed
                table-responsive
            "
        >

            <thead>

            <tr>

                <th>
                    ID Pendaftaran
                </th>

                <th>
                    Nama
                </th>

                <th>
                    Asal Sekolah
                </th>

                <th class="text-center">
                    Nilai
                </th>

            </tr>

            </thead>


            <tbody>

            @forelse (
                $results
                as $result
            )

                <tr>

                    <td>

                        <strong>
                            {{ $result->no_induk }}
                        </strong>

                    </td>


                    <td>
                        {{ $result->nama }}
                    </td>


                    <td>

                        {{
                            $result->sekolah_asal
                            ?: '-'
                        }}

                    </td>


                    <td
                        class="
                            text-center
                            score
                        "
                    >

                        {{
                            rtrim(
                                rtrim(
                                    number_format(
                                        (float)
                                        $result->nilai,
                                        2,
                                        '.',
                                        ''
                                    ),
                                    '0'
                                ),
                                '.'
                            )
                        }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="4"
                        class="text-center"
                    >

                        Belum ada hasil
                        untuk ditampilkan.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>


<script
    src="{{
        asset(
            'assets/assets/vendor/jquery.min.js'
        )
    }}"
></script>


<script
    src="{{
        asset(
            'js/jquery.backstretch.min.js'
        )
    }}"
></script>


<script>

$(document).ready(function () {

    if (
        typeof $.backstretch
        === 'function'
    ) {

        $.backstretch(
            '{{ asset('img/bg_guru.jpg') }}',
            {
                speed: 150
            }
        );

    }

});

</script>

</body>

</html>
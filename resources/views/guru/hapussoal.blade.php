<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="
            width=device-width,
            initial-scale=1
        "
    >

    <title>
        Hapus Paket Soal
    </title>


    <link
        rel="stylesheet"
        href="{{
            asset(
                'lib/fontawesome/css/font-awesome.min.css'
            )
        }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >

</head>


<body>


<section>

    <div class="container">

        <div
            class="alert alert-danger"
            style="margin-top:30px;"
        >

            <i
                class="
                    fa
                    fa-exclamation-triangle
                "
            ></i>


            <strong>
                Yakin Paket Soal ini akan dihapus?
            </strong>


            <p style="margin-top:10px;">

                Paket:

                <b>
                    {{ $soal->paket }}
                </b>

            </p>


            <p>

                Data yang sudah dihapus tidak dapat
                dikembalikan.

            </p>


            <form
                action="{{
                    route(
                        'guru.soal.destroy',
                        $soal->id
                    )
                }}"
                method="POST"
                style="display:inline;"
            >

                @csrf


                <button
                    type="submit"
                    class="btn btn-success btn-sm"
                >

                    <i class="fa fa-trash-o"></i>

                    Hapus

                </button>

            </form>


            <button
                type="button"
                class="btn btn-primary btn-sm"
                onclick="window.close();"
            >

                <i class="fa fa-ban"></i>

                Batal

            </button>

        </div>

    </div>

</section>


</body>

</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        rel="icon"
        href="{{ asset('img/favicon.png') }}"
    >

    <title>Login | Aplikasi Ujian Berbasis Komputer</title>

    <link
        rel="stylesheet"
        href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/login.css') }}"
    >
</head>

<body>

<div class="container">
    @yield('content')
</div>

<script src="{{ asset('assets/assets/vendor/jquery.min.js') }}"></script>

<script src="{{ asset('lib/bootstrap/js/bootstrap.js') }}"></script>

<script src="{{ asset('js/jquery.backstretch.min.js') }}"></script>

<script>
    $.backstretch(
        "{{ asset('img/bg2.jpg') }}",
        {
            speed: 150
        }
    );

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

</body>
</html>
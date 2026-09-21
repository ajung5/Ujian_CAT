<!DOCTYPE html>
<html class="bootstrap-layout">

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <meta charset="utf-8">

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <title>@yield('title')</title>

    <meta
        name="robots"
        content="noindex"
    >

    <link
        href="{{ url('/assets/assets/css/googlefont.css') }}"
        rel="stylesheet"
    >

    <link
        type="text/css"
        href="{{ url('/assets/assets/libs/font-awesome/css/font-awesome.min.css') }}"
        rel="stylesheet"
    >

    <link
        type="text/css"
        href="{{ url('/assets/assets/css/style.min.css') }}"
        rel="stylesheet"
    >

    @stack('styles')
</head>

<body
    class="layout-container ls-top-navbar si-l3-md-up"
    onkeydown="return (event.keyCode != 116)"
>

<script type="text/javascript">
    window.onload = function () {
        document.onkeydown = function (e) {
            return (e.which || e.keyCode) != 116;
        };
    };
</script>

<!-- Navbar -->
<nav
    class="
        navbar
        navbar-dark
        bg-primary
        navbar-full
        navbar-fixed-top
    "
>

    <!-- Toggle sidebar -->
    <button
        class="navbar-toggler pull-xs-left"
        type="button"
        data-toggle="sidebar"
        data-target="#sidebarLeft"
    >
        <i
            class="fa fa-bars"
            aria-hidden="true"
        ></i>
    </button>

    <!-- Brand -->
    <a
        href="{{ route('siswa.index') }}"
        class="navbar-brand"
    >
        <i
            class="fa fa-graduation-cap"
            aria-hidden="true"
        ></i>

        Ujian
    </a>

    <ul class="nav navbar-nav hidden-sm-down">
    </ul>

    @php
        $foto = ! empty(auth()->user()->gambar)
            ? auth()->user()->gambar
            : 'siswa.png';
    @endphp

    <!-- User menu -->
    <ul class="nav navbar-nav pull-xs-right">

        <li class="nav-item dropdown">

            <a
                class="
                    nav-link
                    active
                    dropdown-toggle
                    p-a-0
                "
                data-toggle="dropdown"
                href="#"
                role="button"
                aria-haspopup="false"
            >
                <img
                    src="{{ url('/img/'.$foto) }}"
                    alt="Avatar"
                    class="img-circle"
                    width="40"
                >
            </a>

            <div
                class="
                    dropdown-menu
                    dropdown-menu-right
                    dropdown-menu-list
                "
                aria-labelledby="Preview"
            >

                <a
                    class="dropdown-item"
                    href="{{ route('siswa.profile') }}"
                >
                    <i
                        class="fa fa-user-circle"
                        aria-hidden="true"
                    ></i>

                    <span class="icon-text">
                        Profile
                    </span>
                </a>

                <a
                    class="dropdown-item"
                    href="#"
                    onclick="
                        event.preventDefault();
                        document
                            .getElementById('logout-form')
                            .submit();
                    "
                >
                    <i
                        class="fa fa-sign-out"
                        aria-hidden="true"
                    ></i>

                    <span class="icon-text">
                        Logout
                    </span>
                </a>

            </div>

        </li>

    </ul>

</nav>
<!-- END Navbar -->


@php
    $url = request()->segment(1);
@endphp


<!-- Sidebar -->
<div
    id="sidebarLeft"
    class="
        sidebar
        sidebar-left
        sidebar-light
        sidebar-visible-md-up
        si-si-3
        ls-top-navbar-xs-up
        sidebar-transparent-md
    "
    style="background:#fff !important;"
    data-scrollable
>

    <div class="sidebar-heading">
        Navigasi
    </div>

    <ul class="sidebar-menu">

        <!-- Home -->
        <li
            class="
                sidebar-menu-item
                {{ $url === 'siswa' ? 'active' : '' }}
            "
        >
            <a
                class="sidebar-menu-button"
                href="{{ route('siswa.index') }}"
            >
                <i
                    class="
                        sidebar-menu-icon
                        fa
                        fa-home
                    "
                    aria-hidden="true"
                ></i>

                Home
            </a>
        </li>


        <!-- Profil -->
        <li
            class="
                sidebar-menu-item
                {{ $url === 'profil-siswa' ? 'active' : '' }}
            "
        >
            <a
                class="sidebar-menu-button"
                href="{{ route('siswa.profile') }}"
            >
                <i
                    class="
                        sidebar-menu-icon
                        fa
                        fa-user
                    "
                    aria-hidden="true"
                ></i>

                Profil
            </a>
        </li>


        <!-- Latihan -->
        <li
            class="
                sidebar-menu-item
                {{ $url === 'latihan' ? 'active' : '' }}
            "
        >
            <a
                class="sidebar-menu-button"
                href="{{ route('siswa.latihan') }}"
            >
                <i
                    class="
                        sidebar-menu-icon
                        fa
                        fa-pencil-square-o
                    "
                    aria-hidden="true"
                ></i>

                Latihan Materi
            </a>
        </li>


        <!-- Hasil -->
        <li
            class="
                sidebar-menu-item
                {{ $url === 'hasil-siswa' ? 'active' : '' }}
            "
        >
            <a
                class="sidebar-menu-button"
                href="{{ route('siswa.results') }}"
            >
                <i
                    class="
                        sidebar-menu-icon
                        fa
                        fa-book
                    "
                    aria-hidden="true"
                ></i>

                Hasil Ujian
            </a>
        </li>


        <!-- Soal -->
        <li
            class="
                sidebar-menu-item
                {{ $url === 'soal-siswa' ? 'active' : '' }}
            "
        >
            <a
                class="sidebar-menu-button"
                href="{{ route('siswa.soal') }}"
            >
                <i
                    class="
                        sidebar-menu-icon
                        fa
                        fa-list-alt
                    "
                    aria-hidden="true"
                ></i>

                Soal Ujian
            </a>
        </li>


        <!-- Logout -->
        <li class="sidebar-menu-item">

            <a
                class="sidebar-menu-button"
                href="#"
                onclick="
                    event.preventDefault();
                    document
                        .getElementById('logout-form')
                        .submit();
                "
            >
                <i
                    class="
                        sidebar-menu-icon
                        fa
                        fa-sign-out
                    "
                    aria-hidden="true"
                ></i>

                Logout
            </a>

        </li>

    </ul>

</div>
<!-- END Sidebar -->


<!-- Content -->
<div
    class="layout-content"
    data-scrollable
>

    <div class="container-fluid">

        <ol class="breadcrumb">
            @yield('breadcrumb')
        </ol>

        <div class="row">
            @yield('content')
        </div>

        <div class="row">

            <div
                class="col-md-12"
                style="color:#b3bfd1;"
            >

                <hr>

                &copy;
                Copyright
                2016 -
                {{ date('Y') }}
                <span class="pull-right">
                    V 2.0
                </span>

            </div>

        </div>

    </div>

</div>
<!-- END Content -->


<!-- jQuery -->
<script
    src="{{ url('/assets/assets/vendor/jquery.min.js') }}"
></script>

<!-- Bootstrap -->
<script
    src="{{ url('/assets/assets/vendor/tether.min.js') }}"
></script>

<script
    src="{{ url('/assets/assets/vendor/bootstrap.min.js') }}"
></script>

<!-- AdminPlus -->
<script
    src="{{ url('/assets/assets/vendor/adminplus.js') }}"
></script>

<!-- App JS -->
<script
    src="{{ url('/assets/assets/js/main.min.js') }}"
></script>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN':
                $('meta[name="csrf-token"]')
                    .attr('content')
        }
    });
</script>


@stack('scripts')


<!-- Logout Form -->
<form
    id="logout-form"
    method="POST"
    action="{{ route('logout') }}"
    style="display:none;"
>
    @csrf
</form>


</body>
</html>
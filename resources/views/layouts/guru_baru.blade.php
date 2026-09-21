<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Ujian CAT')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('lib/fontawesome/css/font-awesome.min.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >

    <link
        rel="icon"
        href="{{ asset('img/favicon.png') }}"
    >

    <script src="{{ asset('js/modernizr.js') }}"></script>

    <link
        rel="stylesheet"
        href="{{ asset('lib/Hover/hover.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('lib/weather-icons/css/weather-icons.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('lib/jquery-toggles/toggles-full.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('lib/morrisjs/morris.css') }}"
    >

    @stack('styles')

</head>


<body>

<header>

    <div
        class="headerpanel"
        style="background: #fcfdff"
    >

        <div
            class="logopanel"
            style="background: #00050f"
        >

            <h2>
                <a
                    href="{{ route('guru.index') }}"
                    style="color: #fff"
                >
                    Ujian
                </a>
            </h2>

        </div>


        <div class="headerbar">

            <a
                id="menuToggle"
                class="menutoggle"
            >
                <i class="fa fa-bars"></i>
            </a>


            <div class="header-right">

                <ul class="headermenu">

                    <li>

                        <div class="btn-group">

                            <button
                                type="button"
                                class="btn btn-logged"
                                data-toggle="dropdown"
                            >
                                {{ str(auth()->user()->nama)->before(' ') }}

                                <span class="caret"></span>
                            </button>


                            <ul class="dropdown-menu pull-right">

                                <li>
                                    <a href="{{ url('/profil-guru') }}">
                                        <i class="fa fa-user"></i>
                                        Profil
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('logout') }}">
                                        <i class="fa fa-sign-out"></i>
                                        Log Out
                                    </a>
                                </li>

                            </ul>

                        </div>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</header>


<section>

    @php
        $url = request()->segment(1);
    @endphp


    <div class="leftpanel">

        <div class="leftpanelinner">


            <div class="media leftpanel-profile">

                <div class="media-left">

                    <a href="{{ url('/profil-guru') }}">

                        @if (empty($user->gambar))

                            <img
                                src="{{ asset('img/guru.png') }}"
                                alt="Foto Guru"
                                class="media-object img-thumbnail"
                            >

                        @else

                            <img
                                src="{{ asset('img/' . $user->gambar) }}"
                                alt="{{ $user->nama }}"
                                class="media-object img-thumbnail"
                            >

                        @endif

                    </a>

                </div>


                <div class="media-body">

                    <h4 class="media-heading">

                        {{ str(auth()->user()->nama)->before(' ') }}

                    </h4>

                    <span>

                        @if (auth()->user()->status === 'A')
                            Administrator
                        @else
                            Guru
                        @endif

                    </span>

                </div>

            </div>


            <ul class="nav nav-tabs nav-justified nav-sidebar">

                <li
                    class="tooltips active"
                    data-toggle="tooltip"
                    title="Main Menu"
                >
                    <a
                        data-toggle="tab"
                        data-target="#mainmenu"
                    >
                        <i class="tooltips fa fa-home"></i>
                    </a>
                </li>


                <li
                    class="tooltips"
                    data-toggle="tooltip"
                    title="Log Out"
                >

                    <a href="{{ route('logout') }}">
                        <i class="fa fa-sign-out"></i>
                    </a>

                </li>

            </ul>


            <div class="tab-content">

                <div
                    class="tab-pane active"
                    id="mainmenu"
                >

                    <ul class="nav nav-pills nav-stacked nav-quirk">

                        <li class="{{ $url === 'guru' ? 'active' : '' }}">

                            <a href="{{ route('guru.index') }}">

                                <i class="fa fa-home"></i>

                                <span>
                                    Dashboard
                                </span>

                            </a>

                        </li>

                    </ul>


                    <ul class="nav nav-pills nav-stacked nav-quirk">


                        {{-- MASTER DATA --}}

                        <li
                            class="nav-parent
                            {{
                                in_array(
                                    $url,
                                    [
                                        'data-guru',
                                        'detail-guru',
                                        'kelas',
                                        'detail-kelas',
                                        'data-siswa',
                                        'detail-kelas-siswa'
                                    ],
                                    true
                                )
                                ? 'active'
                                : ''
                            }}"
                        >

                            <a href="#">

                                <i class="fa fa-database"></i>

                                <span>
                                    Master Data
                                </span>

                            </a>


                            <ul class="children">

                                <li
                                    class="{{
                                        in_array(
                                            $url,
                                            ['data-guru', 'detail-guru'],
                                            true
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >

                                    <a href="{{ url('/data-guru') }}">

                                        <i class="fa fa-user"></i>

                                        Guru

                                    </a>

                                </li>


                                <li
                                    class="{{
                                        in_array(
                                            $url,
                                            ['kelas', 'detail-kelas'],
                                            true
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >

                                    <a href="{{ url('/kelas') }}">

                                        <i class="fa fa-building"></i>

                                        Kelas

                                    </a>

                                </li>


                                <li
                                    class="{{
                                        in_array(
                                            $url,
                                            [
                                                'data-siswa',
                                                'detail-kelas-siswa'
                                            ],
                                            true
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >

                                    <a href="{{ url('/data-siswa') }}">

                                        <i class="fa fa-user"></i>

                                        Siswa

                                    </a>

                                </li>

                            </ul>

                        </li>


                        {{-- E-LEARNING --}}

                        <li
                            class="nav-parent
                            {{
                                in_array(
                                    $url,
                                    [
                                        'materi',
                                        'soal-guru',
                                        'detail-soal',
                                        'ubah-detail-soal',
                                        'edit-soal',
                                        'hasil-guru',
                                        'detail-hasil'
                                    ],
                                    true
                                )
                                ? 'active'
                                : ''
                            }}"
                        >

                            <a href="#">

                                <i class="fa fa-graduation-cap"></i>

                                <span>
                                    E-Learning
                                </span>

                            </a>


                            <ul class="children">

                                <li
                                    class="{{ $url === 'materi' ? 'active' : '' }}"
                                >

                                    <a href="{{ url('/materi') }}">
                                        Materi
                                    </a>

                                </li>


                                <li
                                    class="{{
                                        in_array(
                                            $url,
                                            [
                                                'soal-guru',
                                                'detail-soal',
                                                'ubah-detail-soal'
                                            ],
                                            true
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >

                                    <a href="{{ url('/soal-guru') }}">
                                        Soal
                                    </a>

                                </li>


                                <li
                                    class="{{
                                        in_array(
                                            $url,
                                            ['hasil-guru',
                                            'detail-hasil',
                                            'detail-hasil-soal'],
                                            true
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >

                                    <a href="{{ route('guru.results') }}">
                                        Laporan
                                    </a>

                                </li>

                            </ul>

                        </li>


                        <li
                            class="{{ $url === 'profil-guru' ? 'active' : '' }}"
                        >

                            <a href="{{ url('/profil-guru') }}">

                                <i class="fa fa-cog"></i>

                                Pengaturan

                            </a>

                        </li>


                        <li>

                            <a href="{{ route('logout') }}">

                                <i class="fa fa-sign-out"></i>

                                Logout

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>


    <div class="mainpanel">

        <div class="contentpanel">

            <div class="row">

                @yield('content')

            </div>


            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-primary">

                        <div class="panel-body">

                            Copyright &copy;
                            2016 - {{ date('Y') }}

                            <a
                                href="http://www.tipa.co.id"
                                target="_blank"
                            >
                                Tipamedia
                            </a>

                            <span class="pull-right">
                                versi 1.1
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<script src="{{ asset('assets/assets/vendor/jquery.min.js') }}"></script>

<script src="{{ asset('lib/jquery-ui/jquery-ui.js') }}"></script>

<script src="{{ asset('lib/bootstrap/js/bootstrap.js') }}"></script>

<script src="{{ asset('lib/jquery-toggles/toggles.js') }}"></script>

<script src="{{ asset('js/quirk.js') }}"></script>

<script src="{{ asset('js/jquery.backstretch.min.js') }}"></script>


<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN':
                $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.backstretch(
        "{{ asset('img/bg2.jpg') }}",
        {
            speed: 150
        }
    );

</script>

@stack('scripts')

</body>
</html>
@extends('layouts.login')

@section('content')

<link
    rel="icon"
    href="{{ asset('img/favicon.png') }}"
>

<hr class="prettyline">

<div
    class="bungkuslogin"
    style="
        background-color: rgba(255, 150, 13, 0.5);
        color: #d5d9e2;
        padding: 15px;
    "
>

    <center>

        <h2>
            Aplikasi Ujian Berbasis Komputer
        </h2>

        <h1>
            <b>
                {{ $school?->nama ?? 'Ujian CAT' }}
            </b>
        </h1>

        <h3>
            Silahkan Login untuk mengakses halaman Aplikasi Ujian
        </h3>

        <br>

        <a href="{{ url('/') }}">
            <button
                type="button"
                class="btn btn-success btn-lg"
                data-toggle="tooltip"
                title="Kembali kehalaman depan"
            >
                <span class="glyphicon glyphicon-home"></span>
                Home
            </button>
        </a>

        <button
            class="btn btn-primary btn-lg"
            data-toggle="modal"
            data-target=".bs-modal-sm"
            style="margin: 15px 0 15px 0;"
            id="logtooltip"
            title="Login ke halaman Anda"
        >
            <span class="glyphicon glyphicon-lock"></span>
            Login
        </button>

    </center>

    @if ($errors->any())

        <div class="alert alert-danger">

            <ul>

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif

</div>

<hr class="prettyline">


<div
    class="modal fade bs-modal-sm"
    id="myModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="mySmallModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-sm">

        <div class="modal-content">

            <br>

            <div class="bs-example bs-example-tabs">

                <ul
                    id="myTab"
                    class="nav nav-tabs"
                >

                    <li class="active">
                        <a
                            href="#signin"
                            data-toggle="tab"
                        >
                            Sign In
                        </a>
                    </li>

                    <li>
                        <a
                            href="#why"
                            data-toggle="tab"
                        >
                            About?
                        </a>
                    </li>

                </ul>

            </div>


            <div class="modal-body">

                <div
                    id="myTabContent"
                    class="tab-content"
                >

                    <div
                        class="tab-pane fade"
                        id="why"
                    >

                        <p>
                            Aplikasi ujian ini dikembangkan dengan desain
                            responsive sehingga dapat diakses melalui
                            Laptop, Tablet, maupun Smartphone.
                        </p>

                    </div>


                    <div
                        class="tab-pane fade active in"
                        id="signin"
                    >

                        <form
                            method="POST"
                            action="{{ route('login.process') }}"
                        >

                            @csrf

                            <fieldset>

                                <div class="control-group">

                                    <label
                                        class="control-label"
                                        for="email"
                                    >
                                        Email:
                                    </label>

                                    <div class="controls">

                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            class="form-control"
                                            value="{{ old('email') }}"
                                            placeholder="Email"
                                            autocomplete="email"
                                            required
                                            autofocus
                                        >

                                    </div>

                                </div>


                                <div class="control-group">

                                    <label
                                        class="control-label"
                                        for="password"
                                    >
                                        Password:
                                    </label>

                                    <div class="controls">

                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control"
                                            id="password"
                                            placeholder="Password"
                                            autocomplete="current-password"
                                            required
                                        >

                                    </div>

                                </div>


                                <div class="control-group">

                                    <label
                                        class="control-label"
                                        for="remember"
                                    >
                                        &nbsp;
                                    </label>

                                    <div class="controls">

                                        <label
                                            class="checkbox inline"
                                            for="remember"
                                        >

                                            <input
                                                type="checkbox"
                                                name="remember"
                                                id="remember"
                                                value="1"
                                                style="margin: 0;"
                                            >

                                            <span
                                                style="
                                                    margin-left: 25px;
                                                "
                                            >
                                                Remember me
                                            </span>

                                        </label>

                                    </div>

                                </div>


                                <div class="control-group">

                                    <div class="controls">

                                        <button
                                            id="signin"
                                            type="submit"
                                            class="btn btn-success"
                                        >
                                            Login
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-danger"
                                            data-dismiss="modal"
                                        >
                                            Batal
                                        </button>

                                    </div>

                                </div>

                            </fieldset>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
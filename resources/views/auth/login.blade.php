<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Ujian CAT</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 32px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            text-align: center;
            font-size: 25px;
        }

        .school {
            margin-bottom: 28px;
            text-align: center;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .remember label {
            margin: 0;
            font-weight: normal;
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 6px;
            padding: 12px;
            background: #2563eb;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .error {
            margin-bottom: 20px;
            padding: 12px;
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            color: #991b1b;
        }

        .error ul {
            margin: 0;
            padding-left: 20px;
        }

        .info {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

<div class="login-container">

    <h1>Aplikasi Ujian Berbasis Komputer</h1>

    <div class="school">
        {{ $school?->nama ?? 'Ujian CAT' }}
    </div>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('login.process') }}"
    >
        @csrf

        <div class="form-group">
            <label for="email">
                Email
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
                required
            >
        </div>

        <div class="form-group">
            <label for="password">
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >
        </div>

        <div class="remember">
            <input
                id="remember"
                type="checkbox"
                name="remember"
                value="1"
            >

            <label for="remember">
                Remember me
            </label>
        </div>

        <button type="submit">
            Login
        </button>
    </form>

    <div class="info">
        Ujian CAT &mdash; Laravel 13 Migration
    </div>

</div>

</body>
</html>
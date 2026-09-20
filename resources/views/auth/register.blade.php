@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li></li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ url('/auth/register') }}">
    {!! csrf_field() !!}

    <div>
        Name
        <input type="text" name="nama" value="">
    </div>

    <div>
        Email
        <input type="email" name="email" value="">
    </div>

    <div>
        Password
        <input type="password" name="password">
    </div>

    <div>
        Confirm Password
        <input type="password" name="password_confirmation">
    </div>

    <div>
        <button type="submit">Register</button>
    </div>
</form>
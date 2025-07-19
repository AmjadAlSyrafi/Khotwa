<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>register</title>
</head>
<body>
    <h2>register new account  </h2>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>username :</label>
        <input type="text" name="username" value="{{ old('username') }}"><br>

        <label>email :</label>
        <input type="email" name="email" value="{{ old('email') }}"><br>

        <label> password:</label>
        <input type="password" name="password"><br>

        <label>making sure off password  :</label>
        <input type="password" name="password_confirmation"><br>

        <button type="submit">register</button>
    </form>
</body>
</html>

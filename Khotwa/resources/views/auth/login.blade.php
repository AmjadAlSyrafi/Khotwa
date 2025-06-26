<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title> login </title>
</head>
<body>
    <h2>login </h2>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>email :</label>
        <input type="email" name="email" value="{{ old('email') }}"><br>

        <label>password :</label>
        <input type="password" name="password"><br>

        <button type="submit">login </button>
    </form>
</body>
</html>

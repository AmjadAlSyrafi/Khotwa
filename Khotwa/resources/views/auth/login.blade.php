<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
</head>
<body>
    <h2>تسجيل الدخول</h2>

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
        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" value="{{ old('email') }}"><br>

        <label>كلمة المرور:</label>
        <input type="password" name="password"><br>

        <button type="submit">تسجيل الدخول</button>
    </form>
</body>
</html>

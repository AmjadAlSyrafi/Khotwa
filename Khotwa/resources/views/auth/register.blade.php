<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التسجيل</title>
</head>
<body>
    <h2>تسجيل حساب جديد</h2>

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
        <label>اسم المستخدم:</label>
        <input type="text" name="username" value="{{ old('username') }}"><br>

        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" value="{{ old('email') }}"><br>

        <label>كلمة المرور:</label>
        <input type="password" name="password"><br>

        <label>تأكيد كلمة المرور:</label>
        <input type="password" name="password_confirmation"><br>

        <button type="submit">تسجيل</button>
    </form>
</body>
</html>

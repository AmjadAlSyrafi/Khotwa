<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تفعيل البريد</title>
</head>
<body>
    <h2>تفعيل البريد الإلكتروني</h2>

    @if (session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <p>يرجى التحقق من بريدك الإلكتروني لتفعيل الحساب.</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">إعادة إرسال رابط التفعيل</button>
    </form>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>on email </title>
</head>
<body>
    <h2>on email  </h2>

    @if (session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <p> please check your email</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"> resend link verify </button>
    </form>
</body>
</html>

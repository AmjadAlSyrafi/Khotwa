<h2>استعادة كلمة المرور</h2>

@if (session('status'))
    <p style="color: green;">{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label>البريد الإلكتروني:</label>
    <input type="email" name="email" required>
    <button type="submit">إرسال رابط الاستعادة</button>
</form>

<h2>إعادة تعيين كلمة المرور</h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <label>البريد الإلكتروني:</label>
    <input type="email" name="email" required><br>

    <label>كلمة المرور الجديدة:</label>
    <input type="password" name="password" required><br>

    <label>تأكيد كلمة المرور:</label>
    <input type="password" name="password_confirmation" required><br>

    <button type="submit">تحديث</button>
</form>

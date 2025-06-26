<h2> reset password </h2>

@if (session('status'))
    <p style="color: green;">{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label>email :</label>
    <input type="email" name="email" required>
    <button type="submit"> send reset link </button>
</form>

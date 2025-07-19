<h2> reset password </h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <label>email :</label>
    <input type="email" name="email" required><br>

    <label>  new password :</label>
    <input type="password" name="password" required><br>

    <label> making sure off password :</label>
    <input type="password" name="password_confirmation" required><br>

    <button type="submit"> update </button>
</form>

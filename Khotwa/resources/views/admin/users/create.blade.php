<h2> add new user </h2>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <label>name:</label>
    <input type="text" name="username" required><br>

    <label>email:</label>
    <input type="email" name="email" required><br>

    <label> password:</label>
    <input type="password" name="password" required><br>

    <label>role:</label>
    <select name="role_id" required>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
    </select><br>

    <label> knit titly with volunteer  (an necessary):</label>
    <select name="volunteer_id">
        <option value="">-- no thing  --</option>
        @foreach ($volunteers as $volunteer)
            <option value="{{ $volunteer->id }}">{{ $volunteer->full_name }}</option>
        @endforeach
    </select><br>

    <button type="submit"> create </button>
</form>

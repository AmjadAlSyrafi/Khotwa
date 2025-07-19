<h2> user list </h2>
<a href="{{ route('admin.users.create') }}">إضافة مستخدم جديد</a>

<table border="1">
    <tr>
        <th>username</th>
        <th>email</th>
        <th>role</th>
    </tr>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->name ?? '' }}</td>
        </tr>
    @endforeach
</table>

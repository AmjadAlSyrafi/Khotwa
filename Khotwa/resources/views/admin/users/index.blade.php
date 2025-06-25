<h2>قائمة المستخدمين</h2>
<a href="{{ route('admin.users.create') }}">إضافة مستخدم جديد</a>

<table border="1">
    <tr>
        <th>الاسم</th>
        <th>البريد</th>
        <th>الدور</th>
    </tr>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->name ?? '' }}</td>
        </tr>
    @endforeach
</table>

<h2>إضافة مستخدم جديد</h2>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <label>الاسم:</label>
    <input type="text" name="username" required><br>

    <label>البريد الإلكتروني:</label>
    <input type="email" name="email" required><br>

    <label>كلمة المرور:</label>
    <input type="password" name="password" required><br>

    <label>الدور:</label>
    <select name="role_id" required>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
    </select><br>

    <label>ربط بـ متطوع (اختياري):</label>
    <select name="volunteer_id">
        <option value="">-- لا شيء --</option>
        @foreach ($volunteers as $volunteer)
            <option value="{{ $volunteer->id }}">{{ $volunteer->full_name }}</option>
        @endforeach
    </select><br>

    <button type="submit">إنشاء</button>
</form>

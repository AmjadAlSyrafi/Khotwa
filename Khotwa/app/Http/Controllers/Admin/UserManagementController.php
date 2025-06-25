<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    //
     public function index()
    {
        $users = User::with('role')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $volunteers = Volunteer::all();
        return view('admin.users.create', compact('roles', 'volunteers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        User::create([
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role_id'      => $request->role_id,
            'volunteer_id' => $request->volunteer_id,
            'status'       => 1,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

}

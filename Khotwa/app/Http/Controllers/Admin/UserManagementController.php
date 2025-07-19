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
    $users = \App\Models\User::with('role')->get();
    return response()->json($users);
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'username' => 'required|string|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role_id' => 'required|exists:roles,id',
    ]);

    $user = \App\Models\User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'role_id' => $validated['role_id'],
    ]);

    return response()->json(['message' => 'done create user succesfully ', 'user' => $user], 201);
    }

    public function show($id)
    {
    $user = \App\Models\User::with('role')->find($id);

    if (!$user) {
        return response()->json(['message' => ' user not found '], 404);
    }

    return response()->json($user);
    }

    public function update(Request $request, $id)
    {
    $user = \App\Models\User::find($id);

    if (!$user) {
        return response()->json(['message' => ' user not found'], 404);
    }

    $validated = $request->validate([
        'username' => 'sometimes|required|string|unique:users,username,'.$id,
        'email' => 'sometimes|required|email|unique:users,email,'.$id,
        'password' => 'nullable|min:6',
        'role_id' => 'sometimes|required|exists:roles,id',
    ]);

    if (isset($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return response()->json(['message' => 'done updated succesfully  ', 'user' => $user]);
    }

    public function destroy($id)
    {
    $user = \App\Models\User::find($id);

    if (!$user) {
        return response()->json(['message' => ' user not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => ' done deleted user succefully ']);
    }

}

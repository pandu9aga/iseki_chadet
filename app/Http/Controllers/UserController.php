<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $page = 'user';
        $users = User::all();
        return view('members.user', compact('page', 'users'));
    }

    public function create()
    {
        $page = 'user';
        return view('members.user_create', compact('page'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Username_User' => 'required|unique:users,Username_User|max:20',
            'Name_User' => 'required|string|max:100',
            'Password_User' => 'required'
        ]);

        User::create([
            'Username_User' => $validated['Username_User'],
            'Name_User' => $validated['Name_User'],
            'Password_User' => $validated['Password_User'],
        ]);

        return redirect()->route('user')->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $page = 'user';
        $user = User::findOrFail($id);
        return view('members.user_edit', compact('user', 'page'));
    }

   public function update(Request $request, $id)
{
    $validated = $request->validate([
        'Username_User' => 'required|max:20|unique:users,Username_User,' . $id . ',Id_User',
        'Name_User' => 'required|string|max:100',
        // tidak perlu validasi password karena tidak diedit
    ]);

    $user = User::findOrFail($id);

    $user->update([
        'Username_User' => $validated['Username_User'],
        'Name_User' => $validated['Name_User'],
        // jangan update password di sini
    ]);

    return redirect()->route('user')->with('success', 'Data user berhasil diperbarui');
}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'Data user berhasil dihapus.');
    }
}

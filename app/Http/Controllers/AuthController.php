<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // ini penting supaya model User dikenali

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function register()
    {
        return view('auths.register');
    }

    public function register_create(Request $request)
    {
        // Validasi input
        $validateData = $request->validate([
            'Username_User' => 'required|string|max:50|unique:users,Username_User',
            'Name_User' => 'required|string|max:100',
            'Password_User' => 'required|string|min:6',
        ]);

        // Simpan user baru ke database
        $user = new User();
        $user->Username_User = $validateData['Username_User'];
        $user->Name_User = $validateData['Name_User'];
        $user->Password_User = $validateData['Password_User']; // Belum hashing
        $user->save();

        // Redirect atau respon
        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silahkan Login');
    }

    public function login_process(Request $request)
    {
        $credentials = $request->only('Username_User', 'Password_User');

        // Cari user berdasarkan username dan password
        $user = User::where('Username_User', $credentials['Username_User'])
                    ->where('Password_User', $credentials['Password_User'])
                    ->first();

        if ($user) {
            // Simpan data ke session
            session([
                'login_id' => $user->Id_User,
                'login_name' => $user->Name_User,
            ]);

            return redirect()->route('home'); // diganti ke home karena kamu cuma punya 1 jenis akun
        } else {
            return redirect()->back()->withErrors([
                'login' => 'Login gagal. Periksa kembali username, password, dan akses.'
            ]);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}

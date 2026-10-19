<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Siswa wajib mengganti password terlebih dahulu
            if (
                $user->role === 'siswa' &&
                $user->wajib_ganti_password
            ) {
                return redirect()->route('password.change');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Arahkan siswa yang wajib mengganti password
            if (
                $user->role === 'siswa' &&
                $user->wajib_ganti_password
            ) {
                return redirect()->route('password.change');
            }

            // Langsung menuju dashboard setelah login
            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors([
                'username' => 'NIS/NIP atau password salah.',
            ])
            ->onlyInput('username');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
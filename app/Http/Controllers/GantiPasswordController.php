<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class GantiPasswordController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (
            $user->role !== 'siswa' ||
            !$user->wajib_ganti_password
        ) {
            return redirect()->route('dashboard');
        }

        return view('auth.ganti-password');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (
            $user->role !== 'siswa' ||
            !$user->wajib_ganti_password
        ) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user->password = $request->password;
        $user->wajib_ganti_password = false;
        $user->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password berhasil diganti!');
    }
}
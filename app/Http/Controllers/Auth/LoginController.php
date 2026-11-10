<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login_show()
    {
        if (Auth::check()){
            return $this->login_redirect(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login_aksi(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();

            if ($user->role === 'pasien') {
                if (empty($request->nik) || !preg_match('/^\d{16}$/', $request->nik)) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()
                        ->withErrors(['nik' => 'Pasien wajib mengisi NIK (16 digit).'])
                        ->withInput($request->only('username'));
                }

                $pasien = $user->pasien;

                if (!$pasien || $pasien->nik !== $request->nik) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()
                        ->withErrors(['nik' => 'NIK tidak sesuai dengan akun pasien.'])
                        ->withInput($request->only('username'));
                }
            }

            $request->session()->regenerate();
            return $this->login_redirect($user->role);
        }
        
        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->withInput($request->only('username'));
    }

    public function login_keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Berhasil keluar');
    }

    private function login_redirect(string $role)
    {
        return match ($role) {
            'admin' => redirect()->intended('/dashboard/admin'),
            'dokter' => redirect()->intended('/dashboard/dokter'),
            'pasien' => redirect()->intended('/dashboard/pasien'),
            default => redirect('/login'),
        };
    }
    
}
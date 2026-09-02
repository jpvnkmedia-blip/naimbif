<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Sila masukkan alamat e-mel.',
            'email.email' => 'Format e-mel tidak sah.',
            'password.required' => 'Sila masukkan kata laluan.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat kembali, ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'E-mel atau kata laluan tidak tepat.',
        ])->onlyInput('email');
    }

    public function registerForm()
    {
        return redirect()->route('login')
            ->with('info', 'Pendaftaran akaun pegawai baharu dikendalikan secara berpusat oleh Pentadbir Sistem (Admin) melalui Panel Pengurusan Pegawai.');
    }

    public function register(Request $request)
    {
        return redirect()->route('login')
            ->with('info', 'Pendaftaran akaun pegawai baharu dikendalikan secara berpusat oleh Pentadbir Sistem (Admin) melalui Panel Pengurusan Pegawai.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berjaya log keluar.');
    }
}

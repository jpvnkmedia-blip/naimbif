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
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $jajahans = Application::JAJAHAN_LIST;
        return view('auth.register', compact('jajahans'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:30',
            'role' => 'required|in:pegawai_jajahan,pegawai_negeri,admin',
            'jajahan' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Sila masukkan nama penuh pegawai.',
            'email.required' => 'Sila masukkan alamat e-mel rasmi.',
            'email.unique' => 'Alamat e-mel ini telah didaftarkan dalam sistem.',
            'jawatan.required' => 'Sila masukkan jawatan/gred pegawai.',
            'no_telefon.required' => 'Sila masukkan nombor telefon.',
            'role.required' => 'Sila pilih peranan/peringkat akses pegawai.',
            'password.required' => 'Sila masukkan kata laluan.',
            'password.min' => 'Kata laluan sekurang-kurangnya 6 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ]);

        $jajahan = $validated['role'] === 'pegawai_jajahan' ? ($validated['jajahan'] ?? 'Kota Bharu') : 'Ibu Pejabat Kelantan';

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'jawatan' => $validated['jawatan'],
            'no_telefon' => $validated['no_telefon'],
            'role' => $validated['role'],
            'jajahan' => $jajahan,
            'password' => Hash::make($validated['password']),
        ]);

        // Log Aktiviti Sistem
        SystemNotification::logAndNotify(
            type: 'aktiviti_sistem',
            title: 'Pegawai Baharu Didaftarkan (' . $user->name . ')',
            message: 'Pegawai ' . $user->name . ' (' . $user->jawatan . ' - ' . strip_tags($user->role_badge) . ') telah didaftarkan dengan e-mel ' . $user->email . '.',
            actionUrl: route('admin.dashboard'),
            badgeColor: 'purple',
            icon: 'fas fa-user-plus'
        );

        // Auto login
        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Pendaftaran akaun pegawai berjaya! Selamat datang ke Portal NAIMbif JPVNK, ' . $user->name . '.');
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

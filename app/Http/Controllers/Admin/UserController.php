<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Paparan Senarai Semua Pegawai
     */
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('jawatan', 'LIKE', "%{$search}%")
                  ->orWhere('jajahan', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        $users = $query->paginate(15)->withQueryString();
        $jajahans = Application::JAJAHAN_LIST;

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'negeri' => User::where('role', 'pegawai_negeri')->count(),
            'jajahan' => User::where('role', 'pegawai_jajahan')->count(),
        ];

        return view('admin.users.index', compact('users', 'jajahans', 'stats'));
    }

    /**
     * Tambah Pegawai Baharu (Oleh Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:30',
            'role' => 'required|in:pegawai_jajahan,pegawai_negeri,admin',
            'jajahan' => 'nullable|string',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Sila masukkan nama pegawai.',
            'email.required' => 'Sila masukkan e-mel rasmi.',
            'email.unique' => 'E-mel ini telah digunakan.',
            'password.min' => 'Kata laluan sekurang-kurangnya 6 aksara.',
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

        SystemNotification::logAndNotify(
            type: 'aktiviti_sistem',
            title: 'Pegawai Ditambah oleh Pentadbir (' . $user->name . ')',
            message: 'Pentadbir ' . Auth::user()->name . ' telah mendaftarkan pegawai ' . $user->name . ' (' . $user->jawatan . ').',
            badgeColor: 'purple',
            icon: 'fas fa-user-plus'
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Pegawai ' . $user->name . ' telah berjaya ditambah ke dalam sistem.');
    }

    /**
     * Kemaskini Maklumat Pegawai
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:30',
            'role' => 'required|in:pegawai_jajahan,pegawai_negeri,admin',
            'jajahan' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'jawatan' => $validated['jawatan'],
            'no_telefon' => $validated['no_telefon'],
            'role' => $validated['role'],
            'jajahan' => $validated['role'] === 'pegawai_jajahan' ? ($validated['jajahan'] ?? $user->jajahan) : 'Ibu Pejabat Kelantan',
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Maklumat pegawai ' . $user->name . ' telah berjaya dikemas kini.');
    }

    /**
     * Padam Akaun Pegawai
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak boleh memadam akaun anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akaun pegawai ' . $userName . ' telah berjaya dipadam.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\LivestockInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base query depending on user's role
        $query = Application::query();
        if ($user->role === 'pegawai_jajahan' && !empty($user->jajahan)) {
            // Optional: can filter or show all with highlight
        }

        $totalApplications = Application::count();
        $pendingJajahan = Application::where('status_kelengkapan', 'Dalam Semakan')->count();
        $pendingNegeri = Application::where('syor_permohonan', 'Disokong')
            ->where('status_negeri', 'Menunggu Kelulusan')
            ->count();
        $approvedCount = Application::where('status_negeri', 'Lulus')->count();
        $rejectedCount = Application::where('status_negeri', 'Gagal')
            ->orWhere('syor_permohonan', 'Tidak Disokong')
            ->count();

        $totalLivestock = (int) LivestockInventory::sum('jumlah_baka');
        $totalFemale = (int) LivestockInventory::sum(DB::raw('betina_anak + betina_dara + betina_induk'));
        $totalMale = (int) LivestockInventory::sum(DB::raw('jantan_anak + jantan_pejantan'));

        // Breeds breakdown
        $breedStats = LivestockInventory::select('baka', DB::raw('SUM(jumlah_baka) as total'))
            ->groupBy('baka')
            ->pluck('total', 'baka')
            ->toArray();

        // Jajahan breakdown
        $jajahanStats = Application::select('jajahan', DB::raw('COUNT(*) as total'))
            ->groupBy('jajahan')
            ->pluck('total', 'jajahan')
            ->toArray();

        // Recent applications
        $recentApplications = Application::with('livestockInventories')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalApplications',
            'pendingJajahan',
            'pendingNegeri',
            'approvedCount',
            'rejectedCount',
            'totalLivestock',
            'totalFemale',
            'totalMale',
            'breedStats',
            'jajahanStats',
            'recentApplications'
        ));
    }
}

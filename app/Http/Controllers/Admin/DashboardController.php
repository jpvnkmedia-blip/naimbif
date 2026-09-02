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

        // Base query filtered by user's assigned district if Pegawai Jajahan
        $appQuery = Application::forUser($user);

        $totalApplications = (clone $appQuery)->count();
        $pendingJajahan = (clone $appQuery)->where('status_kelengkapan', 'Dalam Semakan')->count();
        $pendingNegeri = (clone $appQuery)->where('syor_permohonan', 'Disokong')
            ->where('status_negeri', 'Menunggu Kelulusan')
            ->count();
        $approvedCount = (clone $appQuery)->where('status_negeri', 'Lulus')->count();
        $rejectedCount = (clone $appQuery)->where(function ($q) {
            $q->where('status_negeri', 'Gagal')
              ->orWhere('syor_permohonan', 'Tidak Disokong');
        })->count();

        // Scope livestock statistics to user's assigned applications
        $appIds = (clone $appQuery)->pluck('id');

        $totalLivestock = (int) LivestockInventory::whereIn('application_id', $appIds)->sum('jumlah_baka');
        $totalFemale = (int) LivestockInventory::whereIn('application_id', $appIds)
            ->sum(DB::raw('betina_anak + betina_dara + betina_induk'));
        $totalMale = (int) LivestockInventory::whereIn('application_id', $appIds)
            ->sum(DB::raw('jantan_anak + jantan_pejantan'));

        // Breeds breakdown
        $breedStats = LivestockInventory::whereIn('application_id', $appIds)
            ->select('baka', DB::raw('SUM(jumlah_baka) as total'))
            ->groupBy('baka')
            ->pluck('total', 'baka')
            ->toArray();

        // Jajahan breakdown (or status breakdown for specific district)
        $jajahanStats = (clone $appQuery)->select(DB::raw('COALESCE(jajahan_ladang, jajahan) as jajahan_nama'), DB::raw('COUNT(*) as total'))
            ->groupBy('jajahan_nama')
            ->pluck('total', 'jajahan_nama')
            ->toArray();

        // Recent applications
        $recentApplications = (clone $appQuery)->with('livestockInventories')
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

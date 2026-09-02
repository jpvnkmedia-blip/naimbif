<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\LivestockInventory;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationManagementController extends Controller
{
    /**
     * Senarai Semua Permohonan dengan Tapisan
     */
    public function index(Request $request)
    {
        $query = Application::with(['livestockInventories', 'disemakOleh', 'diluluskanOleh']);

        // Filter: Carian teks
        if ($request->filled('q')) {
            $search = $request->q;
            $cleanIc = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanIc) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rujukan', 'like', "%{$search}%")
                  ->orWhere('no_kp', 'like', "%{$cleanIc}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%");
            });
        }

        // Filter: Jajahan
        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        // Filter: Status Kelengkapan / Negeri
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Lulus') {
                $query->where('status_negeri', 'Lulus');
            } elseif ($status === 'Gagal') {
                $query->where('status_negeri', 'Gagal');
            } elseif ($status === 'Disokong') {
                $query->where('syor_permohonan', 'Disokong')->where('status_negeri', 'Menunggu Kelulusan');
            } elseif ($status === 'Tidak Disokong') {
                $query->where('syor_permohonan', 'Tidak Disokong');
            } elseif ($status === 'Dalam Semakan') {
                $query->where('status_kelengkapan', 'Dalam Semakan');
            }
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $applications = $query->paginate(15)->withQueryString();
        $jajahans = Application::JAJAHAN_LIST;

        return view('admin.applications.index', compact('applications', 'jajahans'));
    }

    /**
     * Paparan Perincian & Borang Semakan Pegawai
     */
    public function show($id)
    {
        $application = Application::with(['livestockInventories', 'disemakOleh', 'diluluskanOleh'])
            ->findOrFail($id);

        $inventories = [];
        foreach ($application->livestockInventories as $inv) {
            $inventories[$inv->baka] = $inv;
        }

        return view('admin.applications.show', compact('application', 'inventories'));
    }

    /**
     * Kemas Kini Tindakan Pejabat Jajahan
     */
    public function updateJajahan(Request $request, $id)
    {
        if (!Auth::user()->isPegawaiJajahan()) {
            return redirect()->back()
                ->with('error', 'Akses Disekat: Ruangan Tindakan Pejabat Jajahan hanya boleh diisi oleh Pegawai JPV Jajahan.');
        }

        $application = Application::findOrFail($id);

        $request->validate([
            'id_premis' => 'nullable|string|max:100',
            'status_kelengkapan' => 'required|in:Lengkap,Tidak Lengkap,Dalam Semakan',
            'syor_permohonan' => 'required|in:Disokong,Tidak disokong,Belum Disemak',
            'pegawai_penyiasat' => 'required|string|max:255',
            'tarikh_siasatan' => 'nullable|date',
            'catatan_jajahan' => 'nullable|string',
        ], [
            'pegawai_penyiasat.required' => 'Sila masukkan nama Pegawai Penyiasat.',
        ]);

        $application->update([
            'id_premis' => $request->id_premis,
            'status_kelengkapan' => $request->status_kelengkapan,
            'syor_permohonan' => $request->syor_permohonan,
            'pegawai_penyiasat' => $request->pegawai_penyiasat,
            'tarikh_siasatan' => $request->tarikh_siasatan ?: now()->toDateString(),
            'catatan_jajahan' => $request->catatan_jajahan,
            'tarikh_semakan_jajahan' => now(),
            'disemak_oleh_user_id' => Auth::id(),
            'status_permohonan' => $request->syor_permohonan === 'Disokong' ? 'Disemak Jajahan' : 'Dalam Semakan',
        ]);

        // Log Notifikasi Sistem & Emel
        SystemNotification::logAndNotify(
            type: 'ulasan_jajahan',
            title: 'Ulasan Jajahan Selesai: ' . $application->no_rujukan . ' (' . $application->syor_permohonan . ')',
            message: 'Pegawai Jajahan (' . $application->jajahan . ') telah mengesahkan siasatan premis bagi ' . $application->nama . ' dengan status syor: ' . $application->syor_permohonan . ' (ID Premis: ' . ($application->id_premis ?: '-') . ').',
            application: $application,
            actionUrl: route('admin.applications.show', $application->id),
            badgeColor: 'amber',
            icon: 'fas fa-search-location'
        );

        return redirect()->route('admin.applications.show', $application->id)
            ->with('success', 'Ulasan dan status tindakan Pejabat Jajahan telah berjaya disimpan.');
    }

    /**
     * Kemas Kini Keputusan Pejabat Negeri (Ibu Pejabat JPVNK)
     */
    public function updateNegeri(Request $request, $id)
    {
        if (!Auth::user()->isPegawaiNegeri()) {
            return redirect()->back()
                ->with('error', 'Akses Disekat: Hanya Pegawai Ibu Pejabat JPVNK (Negeri) atau Pentadbir dibenarkan mengemas kini keputusan kelulusan Jabatan.');
        }

        $application = Application::findOrFail($id);

        $request->validate([
            'status_negeri' => 'required|in:Lulus,Gagal,Menunggu Kelulusan',
            'no_rujukan_negeri' => 'nullable|string|max:100',
            'ulasan_negeri' => 'nullable|string',
            'pegawai_pelulus' => 'required|string|max:255',
        ], [
            'pegawai_pelulus.required' => 'Sila masukkan nama Pegawai Pelulus.',
        ]);

        // Auto-generate No. Rujukan Negeri if approved and left blank
        $noRujukanNegeri = $request->no_rujukan_negeri;
        if ($request->status_negeri === 'Lulus' && empty($noRujukanNegeri)) {
            $year = date('Y');
            $count = Application::whereNotNull('no_rujukan_negeri')->count() + 1;
            $noRujukanNegeri = sprintf('JPVNK/BRIDLOT/%s/%04d', $year, $count);
        }

        $application->update([
            'status_negeri' => $request->status_negeri,
            'no_rujukan_negeri' => $noRujukanNegeri,
            'ulasan_negeri' => $request->ulasan_negeri,
            'pegawai_pelulus' => $request->pegawai_pelulus,
            'tarikh_kelulusan_negeri' => now(),
            'diluluskan_oleh_user_id' => Auth::id(),
            'status_permohonan' => $request->status_negeri,
        ]);

        // Log Notifikasi Sistem & Emel
        SystemNotification::logAndNotify(
            type: 'keputusan_jabatan',
            title: 'Keputusan Jabatan: ' . $application->no_rujukan . ' (' . strtoupper($application->status_negeri) . ')',
            message: 'Ibu Pejabat JPVNK telah mengeluarkan keputusan rasmi ' . strtoupper($application->status_negeri) . ' bagi pemohon ' . $application->nama . ' (No. Kelulusan: ' . ($application->no_rujukan_negeri ?: '-') . ').',
            application: $application,
            actionUrl: route('admin.applications.show', $application->id),
            badgeColor: $application->status_negeri === 'Lulus' ? 'emerald' : 'rose',
            icon: 'fas fa-stamp'
        );

        return redirect()->route('admin.applications.show', $application->id)
            ->with('success', 'Keputusan dan ulasan Jabatan telah berjaya dikemas kini.');
    }

    /**
     * Padam Permohonan
     */
    public function destroy($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', 'Permohonan ' . $application->no_rujukan . ' telah berjaya dipadam.');
    }

    /**
     * Eksport Data ke CSV / Excel
     */
    public function exportCsv(Request $request)
    {
        $query = Application::with('livestockInventories');

        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Lulus') {
                $query->where('status_negeri', 'Lulus');
            } elseif ($status === 'Gagal') {
                $query->where('status_negeri', 'Gagal');
            }
        }

        $applications = $query->latest()->get();

        $filename = 'senarai-permohonan-naimbif-' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($file, [
                'No. Rujukan',
                'Tarikh Mohon',
                'Nama Pemohon',
                'No. Kad Pengenalan',
                'No. Telefon',
                'Jajahan Pemohon',
                'Status Penternakan',
                'Pengalaman (Tahun)',
                'Jajahan Ladang',
                'Keluasan Tanah (Ekar)',
                'Padang Ragut',
                'Pekerja (Orang)',
                'Punca Ternakan',
                'Kaedah Pembiakan',
                'Jumlah Lembu',
                'ID Premis Jajahan',
                'Kelengkapan Dokumen',
                'Syor Jajahan',
                'Keputusan Jabatan',
                'No Rujukan Kelulusan',
            ]);

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->no_rujukan,
                    $app->tarikh_permohonan ? $app->tarikh_permohonan->format('d/m/Y') : '',
                    $app->nama,
                    "'" . $app->no_kp,
                    $app->no_telefon,
                    $app->jajahan,
                    $app->status_penternakan,
                    $app->pengalaman_menternak,
                    $app->jajahan_ladang ?: $app->jajahan,
                    $app->keluasan_tanah,
                    $app->padang_ragut,
                    $app->bilangan_pekerja,
                    $app->punca_ternakan,
                    $app->kaedah_pembiakan,
                    $app->total_ternakan,
                    $app->id_premis ?? '-',
                    $app->status_kelengkapan,
                    $app->syor_permohonan,
                    $app->status_negeri,
                    $app->no_rujukan_negeri ?? '-',
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\LivestockInventory;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicApplicationController extends Controller
{
    /**
     * Laman Utama Portal NAIMbif JPVNK
     */
    public function index()
    {
        $totalLulus = Application::where('status_negeri', 'Lulus')->count();
        $totalTernakan = LivestockInventory::sum('jumlah_baka');
        $jajahanCount = count(Application::JAJAHAN_LIST);

        return view('public.home', compact('totalLulus', 'totalTernakan', 'jajahanCount'));
    }

    /**
     * Papar Borang Permohonan Baru
     */
    public function create()
    {
        $jajahans = Application::JAJAHAN_LIST;
        $bakas = Application::BAKA_LIST;

        return view('public.apply', compact('jajahans', 'bakas'));
    }

    /**
     * Simpan Permohonan Baru
     */
    public function store(Request $request)
    {
        $rules = [
            // Maklumat Peserta
            'nama' => 'required|string|max:255',
            'no_kp' => 'required|string|max:20',
            'no_telefon' => 'required|string|max:30',
            'alamat_tetap' => 'required|string',
            'poskod' => 'required|string|max:10',
            'jajahan' => 'required|string|in:' . implode(',', Application::JAJAHAN_LIST),
            'pengalaman_menternak' => 'required|integer|min:0|max:80',
            'status_penternakan' => 'required|string|in:Sepenuh Masa,Sampingan',
            'pernah_kursus' => 'required|in:1,0',
            'nama_kursus' => 'nullable|required_if:pernah_kursus,1|string|max:255',
            'anjuran_kursus' => 'nullable|required_if:pernah_kursus,1|string|max:255',
            'berminat_kursus_jpvnk' => 'nullable|in:1,0',

            // Maklumat Ladang
            'alamat_ladang' => 'nullable|string',
            'poskod_ladang' => 'nullable|string|max:10',
            'jajahan_ladang' => 'nullable|string|in:' . implode(',', Application::JAJAHAN_LIST),
            'gps_longitud' => 'nullable|string|max:50',
            'gps_latitud' => 'nullable|string|max:50',
            'status_tanah' => 'required|string|in:Sendiri,Sewa,Kerajaan,Lain-lain',
            'status_tanah_lain' => 'nullable|required_if:status_tanah,Lain-lain|string|max:255',
            'keluasan_tanah' => 'required|numeric|min:0.1',
            'padang_ragut' => 'required|string|in:Ada,Tiada',
            'bilangan_pekerja' => 'required|integer|min:0',

            // Maklumat Ternakan
            'punca_ternakan' => 'required|string|in:Beli,Pawah,Lain-lain',
            'punca_ternakan_lain' => 'nullable|required_if:punca_ternakan,Lain-lain|string|max:255',
            'kaedah_pembiakan' => 'required|string|in:Asli,Permanian Beradas',

            // Pengakuan
            'pengakuan_benar' => 'accepted',
            'tandatangan' => 'nullable|string',
            'tarikh_permohonan' => 'required|date',

            // Stok Ternakan (Array)
            'stok' => 'required|array',
        ];

        $messages = [
            'nama.required' => 'Sila masukkan Nama Pemohon.',
            'no_kp.required' => 'Sila masukkan No. Kad Pengenalan.',
            'no_telefon.required' => 'Sila masukkan No. Telefon.',
            'alamat_tetap.required' => 'Sila masukkan Alamat Tetap.',
            'poskod.required' => 'Sila masukkan Poskod.',
            'jajahan.required' => 'Sila pilih Jajahan.',
            'keluasan_tanah.required' => 'Sila masukkan Keluasan Tanah (Ekar).',
            'pengakuan_benar.accepted' => 'Sila tandakan pengakuan bahawa butiran di atas adalah benar.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cleanNoKp = preg_replace('/[^0-9]/', '', $request->no_kp);

        // Semakan Sekatan: 1 Permohonan Aktif Sahaja Bagi Setiap No. KP
        $existingActiveApp = Application::where('no_kp', $cleanNoKp)
            ->where(function ($query) {
                $query->whereNull('status_negeri')
                      ->orWhere('status_negeri', '!=', 'Gagal');
            })
            ->latest()
            ->first();

        if ($existingActiveApp) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_error', [
                    'no_kp' => $request->no_kp,
                    'no_rujukan' => $existingActiveApp->no_rujukan,
                    'nama' => $existingActiveApp->nama,
                    'status' => $existingActiveApp->status_permohonan ?: ($existingActiveApp->status_negeri ?: $existingActiveApp->syor_permohonan),
                    'tarikh' => $existingActiveApp->tarikh_permohonan ? $existingActiveApp->tarikh_permohonan->format('d/m/Y') : $existingActiveApp->created_at->format('d/m/Y'),
                ]);
        }

        try {
            DB::beginTransaction();

            $application = Application::create([
                'nama' => strtoupper(trim($request->nama)),
                'no_kp' => $cleanNoKp,
                'no_telefon' => trim($request->no_telefon),
                'alamat_tetap' => trim($request->alamat_tetap),
                'poskod' => trim($request->poskod),
                'jajahan' => $request->jajahan,
                'pengalaman_menternak' => (int) $request->pengalaman_menternak,
                'status_penternakan' => $request->status_penternakan,
                'pernah_kursus' => (bool) $request->pernah_kursus,
                'nama_kursus' => $request->pernah_kursus ? $request->nama_kursus : null,
                'anjuran_kursus' => $request->pernah_kursus ? $request->anjuran_kursus : null,
                'berminat_kursus_jpvnk' => !$request->pernah_kursus ? (bool) $request->berminat_kursus_jpvnk : null,

                'alamat_ladang' => $request->alamat_ladang ?: $request->alamat_tetap,
                'poskod_ladang' => $request->poskod_ladang ?: $request->poskod,
                'jajahan_ladang' => $request->jajahan_ladang ?: $request->jajahan,
                'gps_longitud' => $request->gps_longitud,
                'gps_latitud' => $request->gps_latitud,
                'status_tanah' => $request->status_tanah,
                'status_tanah_lain' => $request->status_tanah === 'Lain-lain' ? $request->status_tanah_lain : null,
                'keluasan_tanah' => $request->keluasan_tanah,
                'padang_ragut' => $request->padang_ragut,
                'bilangan_pekerja' => (int) $request->bilangan_pekerja,

                'punca_ternakan' => $request->punca_ternakan,
                'punca_ternakan_lain' => $request->punca_ternakan === 'Lain-lain' ? $request->punca_ternakan_lain : null,
                'kaedah_pembiakan' => $request->kaedah_pembiakan,

                'pengakuan_benar' => true,
                'tandatangan' => $request->tandatangan,
                'tarikh_permohonan' => $request->tarikh_permohonan ?? now()->toDateString(),
                'status_permohonan' => 'Dihantar',
            ]);

            // Save Livestock Inventories
            if (is_array($request->stok)) {
                foreach ($request->stok as $baka => $data) {
                    $bAnak = (int) ($data['betina_anak'] ?? 0);
                    $bDara = (int) ($data['betina_dara'] ?? 0);
                    $bInduk = (int) ($data['betina_induk'] ?? 0);
                    $jAnak = (int) ($data['jantan_anak'] ?? 0);
                    $jPejantan = (int) ($data['jantan_pejantan'] ?? 0);
                    $total = $bAnak + $bDara + $bInduk + $jAnak + $jPejantan;

                    LivestockInventory::create([
                        'application_id' => $application->id,
                        'baka' => $baka,
                        'nama_baka_lain' => $baka === 'LAIN-LAIN' ? ($data['nama_baka_lain'] ?? null) : null,
                        'betina_anak' => $bAnak,
                        'betina_dara' => $bDara,
                        'betina_induk' => $bInduk,
                        'jantan_anak' => $jAnak,
                        'jantan_pejantan' => $jPejantan,
                        'jumlah_baka' => $total,
                    ]);
                }
            }

            DB::commit();

            // Log Notifikasi Sistem & Notifikasi Emel
            SystemNotification::logAndNotify(
                type: 'permohonan_baru',
                title: 'Permohonan Baru Diterima (' . $application->no_rujukan . ')',
                message: 'Pemohon ' . $application->nama . ' (No. KP: ' . $application->formatted_no_kp . ') telah menghantar permohonan Ladang Bridlot bagi jajahan ' . ($application->jajahan_ladang ?: $application->jajahan) . ' dengan ' . $application->total_ternakan . ' ekor ternakan.',
                application: $application,
                actionUrl: route('admin.applications.show', $application->id),
                badgeColor: 'emerald',
                icon: 'fas fa-file-invoice'
            );

            return redirect()->route('public.success', $application->no_rujukan)
                ->with('success', 'Permohonan anda telah berjaya dihantar.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Ralat semasa memproses permohonan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Laman Kejayaan Permohonan
     */
    public function success($no_rujukan)
    {
        $application = Application::with('livestockInventories')
            ->where('no_rujukan', $no_rujukan)
            ->firstOrFail();

        return view('public.success', compact('application'));
    }

    /**
     * Laman Carian Semakan Status
     */
    public function checkStatus(Request $request)
    {
        $query = $request->input('carian');
        $application = null;
        $searched = false;

        if ($query) {
            $searched = true;
            $cleanIc = preg_replace('/[^0-9]/', '', $query);

            $application = Application::with(['livestockInventories', 'disemakOleh', 'diluluskanOleh'])
                ->where(function ($q) use ($query, $cleanIc) {
                    $q->where('no_rujukan', strtoupper(trim($query)))
                      ->orWhere('no_kp', $cleanIc)
                      ->orWhere('no_kp', $query);
                })
                ->latest()
                ->first();
        }

        if ($query && $application) {
            session()->forget("verified_applicant_{$application->no_rujukan}");
        }

        return view('public.check_status', compact('application', 'query', 'searched'));
    }

    /**
     * Pengesahan Keselamatan Dwi-Faktor Pemohon (2FA: No. KP + No. Telefon)
     */
    public function verifyEdit(Request $request, $no_rujukan)
    {
        $application = Application::where('no_rujukan', $no_rujukan)->firstOrFail();

        if ($request->isMethod('get')) {
            return redirect()->route('public.edit', $no_rujukan);
        }

        $request->validate([
            'no_kp' => 'required|string',
            'no_telefon' => 'required|string',
        ], [
            'no_kp.required' => 'Sila masukkan No. Kad Pengenalan pemohon.',
            'no_telefon.required' => 'Sila masukkan No. Telefon atau 4 digit terakhir no. telefon pemohon.',
        ]);

        $inputIc = preg_replace('/[^0-9]/', '', $request->no_kp);
        $inputPhone = preg_replace('/[^0-9]/', '', $request->no_telefon);
        $appPhone = preg_replace('/[^0-9]/', '', $application->no_telefon);

        // Semak No. KP
        $icMatched = ($inputIc === $application->no_kp);

        // Semak No. Telefon (Nombor Penuh ATAU 4 Digit Terakhir)
        $phoneMatched = false;
        if (!empty($inputPhone) && !empty($appPhone)) {
            if ($inputPhone === $appPhone) {
                $phoneMatched = true;
            } elseif (strlen($inputPhone) >= 4 && str_ends_with($appPhone, $inputPhone)) {
                $phoneMatched = true;
            }
        }

        if (!$icMatched || !$phoneMatched) {
            return redirect()->back()
                ->with('error', 'Pengesahan Dwi-Faktor Gagal: Padanan No. Kad Pengenalan atau No. Telefon tidak tepat. Akses disekat demi keselamatan peribadi penternak.')
                ->withInput();
        }

        // Simpan sesi pengesahan
        session(["verified_applicant_{$application->no_rujukan}" => $application->no_kp]);

        return redirect()->route('public.edit', $application->no_rujukan)
            ->with('success', 'Pengesahan dwi-faktor berjaya. Anda kini boleh mengemas kini maklumat permohonan.');
    }

    /**
     * Paparan Borang Kemaskini Permohonan Pemohon
     */
    public function edit($no_rujukan)
    {
        $application = Application::with('livestockInventories')
            ->where('no_rujukan', $no_rujukan)
            ->firstOrFail();

        // KESELAMATAN: Semak sama ada pemohon telah lulus pengesahan identiti (No. KP + No. Telefon)
        $verifiedIc = session("verified_applicant_{$application->no_rujukan}");
        if ($verifiedIc !== $application->no_kp) {
            return view('public.verify_edit', compact('application'));
        }

        $jajahans = Application::JAJAHAN_LIST;
        $bakas = Application::BAKA_LIST;

        $inventories = [];
        foreach ($application->livestockInventories as $inv) {
            $inventories[$inv->baka] = $inv;
        }

        return view('public.edit', compact('application', 'jajahans', 'bakas', 'inventories'));
    }

    /**
     * Kemaskini Permohonan Pemohon
     */
    public function update(Request $request, $no_rujukan)
    {
        $application = Application::where('no_rujukan', $no_rujukan)->firstOrFail();

        // KESELAMATAN: Pastikan No KP sepadan dan sesi sah
        $verifiedIc = session("verified_applicant_{$application->no_rujukan}");
        $submittedIc = preg_replace('/[^0-9]/', '', $request->no_kp);

        if ($verifiedIc !== $application->no_kp && $submittedIc !== $application->no_kp) {
            return redirect()->route('public.edit', $application->no_rujukan)
                ->with('error', 'Ralat Keselamatan: Anda tidak mempunyai kebenaran untuk mengemas kini rekod pemohon ini.');
        }

        $rules = [
            // Maklumat Peserta
            'nama' => 'required|string|max:255',
            'no_kp' => 'required|string|max:20',
            'no_telefon' => 'required|string|max:30',
            'alamat_tetap' => 'required|string',
            'poskod' => 'required|string|max:10',
            'jajahan' => 'required|string|in:' . implode(',', Application::JAJAHAN_LIST),
            'pengalaman_menternak' => 'required|integer|min:0|max:80',
            'status_penternakan' => 'required|string|in:Sepenuh Masa,Sampingan',
            'pernah_kursus' => 'required|in:1,0',
            'nama_kursus' => 'nullable|required_if:pernah_kursus,1|string|max:255',
            'anjuran_kursus' => 'nullable|required_if:pernah_kursus,1|string|max:255',
            'berminat_kursus_jpvnk' => 'nullable|in:1,0',

            // Maklumat Ladang
            'alamat_ladang' => 'nullable|string',
            'poskod_ladang' => 'nullable|string|max:10',
            'jajahan_ladang' => 'nullable|string|in:' . implode(',', Application::JAJAHAN_LIST),
            'gps_longitud' => 'nullable|string|max:50',
            'gps_latitud' => 'nullable|string|max:50',
            'status_tanah' => 'required|string|in:Sendiri,Sewa,Kerajaan,Lain-lain',
            'status_tanah_lain' => 'nullable|required_if:status_tanah,Lain-lain|string|max:255',
            'keluasan_tanah' => 'required|numeric|min:0.1',
            'padang_ragut' => 'required|string|in:Ada,Tiada',
            'bilangan_pekerja' => 'required|integer|min:0',

            // Maklumat Ternakan
            'punca_ternakan' => 'required|string|in:Beli,Pawah,Lain-lain',
            'punca_ternakan_lain' => 'nullable|required_if:punca_ternakan,Lain-lain|string|max:255',
            'kaedah_pembiakan' => 'required|string|in:Asli,Permanian Beradas',

            // Stok Ternakan (Array)
            'stok' => 'required|array',
        ];

        $messages = [
            'nama.required' => 'Sila masukkan Nama Pemohon.',
            'no_kp.required' => 'Sila masukkan No. Kad Pengenalan.',
            'no_telefon.required' => 'Sila masukkan No. Telefon.',
            'alamat_tetap.required' => 'Sila masukkan Alamat Tetap.',
            'poskod.required' => 'Sila masukkan Poskod.',
            'jajahan.required' => 'Sila pilih Jajahan.',
            'keluasan_tanah.required' => 'Sila masukkan Keluasan Tanah (Ekar).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'nama' => strtoupper(trim($request->nama)),
                'no_kp' => preg_replace('/[^0-9]/', '', $request->no_kp),
                'no_telefon' => trim($request->no_telefon),
                'alamat_tetap' => trim($request->alamat_tetap),
                'poskod' => trim($request->poskod),
                'jajahan' => $request->jajahan,
                'pengalaman_menternak' => (int) $request->pengalaman_menternak,
                'status_penternakan' => $request->status_penternakan,
                'pernah_kursus' => (bool) $request->pernah_kursus,
                'nama_kursus' => $request->pernah_kursus ? $request->nama_kursus : null,
                'anjuran_kursus' => $request->pernah_kursus ? $request->anjuran_kursus : null,
                'berminat_kursus_jpvnk' => !$request->pernah_kursus ? (bool) $request->berminat_kursus_jpvnk : null,

                'alamat_ladang' => $request->alamat_ladang ?: $request->alamat_tetap,
                'poskod_ladang' => $request->poskod_ladang ?: $request->poskod,
                'jajahan_ladang' => $request->jajahan_ladang ?: $request->jajahan,
                'gps_longitud' => $request->gps_longitud,
                'gps_latitud' => $request->gps_latitud,
                'status_tanah' => $request->status_tanah,
                'status_tanah_lain' => $request->status_tanah === 'Lain-lain' ? $request->status_tanah_lain : null,
                'keluasan_tanah' => $request->keluasan_tanah,
                'padang_ragut' => $request->padang_ragut,
                'bilangan_pekerja' => (int) $request->bilangan_pekerja,

                'punca_ternakan' => $request->punca_ternakan,
                'punca_ternakan_lain' => $request->punca_ternakan === 'Lain-lain' ? $request->punca_ternakan_lain : null,
                'kaedah_pembiakan' => $request->kaedah_pembiakan,
            ];

            if ($request->filled('tandatangan')) {
                $updateData['tandatangan'] = $request->tandatangan;
            }

            $application->update($updateData);

            // Re-create livestock inventories
            $application->livestockInventories()->delete();

            if (is_array($request->stok)) {
                foreach ($request->stok as $baka => $data) {
                    $bAnak = (int) ($data['betina_anak'] ?? 0);
                    $bDara = (int) ($data['betina_dara'] ?? 0);
                    $bInduk = (int) ($data['betina_induk'] ?? 0);
                    $jAnak = (int) ($data['jantan_anak'] ?? 0);
                    $jPejantan = (int) ($data['jantan_pejantan'] ?? 0);
                    $total = $bAnak + $bDara + $bInduk + $jAnak + $jPejantan;

                    LivestockInventory::create([
                        'application_id' => $application->id,
                        'baka' => $baka,
                        'nama_baka_lain' => $baka === 'LAIN-LAIN' ? ($data['nama_baka_lain'] ?? null) : null,
                        'betina_anak' => $bAnak,
                        'betina_dara' => $bDara,
                        'betina_induk' => $bInduk,
                        'jantan_anak' => $jAnak,
                        'jantan_pejantan' => $jPejantan,
                        'jumlah_baka' => $total,
                    ]);
                }
            }

            DB::commit();

            // Log Notifikasi Sistem & Notifikasi Emel
            SystemNotification::logAndNotify(
                type: 'permohonan_dikemaskini',
                title: 'Pindaan Data oleh Pemohon (' . $application->no_rujukan . ')',
                message: 'Pemohon ' . $application->nama . ' telah mengemas kini butiran permohonan ' . $application->no_rujukan . ' (Jajahan: ' . ($application->jajahan_ladang ?: $application->jajahan) . ').',
                application: $application,
                actionUrl: route('admin.applications.show', $application->id),
                badgeColor: 'blue',
                icon: 'fas fa-edit'
            );

            // Clear verified session so subsequent edits require 2FA again
            session()->forget("verified_applicant_{$application->no_rujukan}");

            return redirect()->route('public.check_status', ['carian' => $application->no_rujukan])
                ->with('success', 'Maklumat permohonan ' . $application->no_rujukan . ' telah berjaya dikemas kini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Ralat semasa mengemas kini permohonan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Paparan Borang Rasmi PDF / Cetakan A4 (Salinan Borang Asal JPVNK)
     */
    public function printForm($no_rujukan)
    {
        $application = Application::with(['livestockInventories', 'disemakOleh', 'diluluskanOleh'])
            ->where('no_rujukan', $no_rujukan)
            ->firstOrFail();

        // Index inventories by baka for direct lookup in printable layout
        $inventories = [];
        foreach ($application->livestockInventories as $inv) {
            $inventories[$inv->baka] = $inv;
        }

        return view('public.print', compact('application', 'inventories'));
    }

    /**
     * API Semakan Masa Nyata No. KP (Real-Time IC Lookup)
     */
    public function checkExistingIc(Request $request)
    {
        $rawNoKp = $request->query('no_kp', '');
        $cleanNoKp = preg_replace('/[^0-9]/', '', $rawNoKp);

        if (strlen($cleanNoKp) < 12) {
            return response()->json(['exists' => false]);
        }

        $existingActiveApp = Application::where('no_kp', $cleanNoKp)
            ->where(function ($query) {
                $query->whereNull('status_negeri')
                      ->orWhere('status_negeri', '!=', 'Gagal');
            })
            ->latest()
            ->first();

        if ($existingActiveApp) {
            return response()->json([
                'exists' => true,
                'no_rujukan' => $existingActiveApp->no_rujukan,
                'nama' => $existingActiveApp->nama,
                'status' => $existingActiveApp->status_permohonan ?: ($existingActiveApp->status_negeri ?: $existingActiveApp->syor_permohonan),
                'tarikh' => $existingActiveApp->tarikh_permohonan ? $existingActiveApp->tarikh_permohonan->format('d/m/Y') : $existingActiveApp->created_at->format('d/m/Y'),
                'check_url' => route('public.check_status', ['carian' => $existingActiveApp->no_rujukan]),
                'edit_url' => route('public.edit', $existingActiveApp->no_rujukan),
            ]);
        }

        return response()->json(['exists' => false]);
    }
}

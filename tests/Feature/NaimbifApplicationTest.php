<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\LivestockInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NaimbifApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function createTestApp(array $overrides = []): Application
    {
        $app = Application::create(array_merge([
            'no_rujukan' => 'NB-2026-0001',
            'nama' => 'Wan Muhammad Azlan',
            'no_kp' => '850712035411',
            'no_telefon' => '019-9112233',
            'alamat_tetap' => 'Lot 1422, Kampung Sireh',
            'poskod' => '15050',
            'jajahan' => 'Kota Bharu',
            'pengalaman_menternak' => 8,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => true,
            'nama_kursus' => 'Kursus Penternakan Lembu',
            'anjuran_kursus' => 'JPVNK',
            'alamat_ladang' => 'Lot 889, Mukim Kemumin',
            'poskod_ladang' => '16100',
            'jajahan_ladang' => 'Kota Bharu',
            'gps_longitud' => '102.291240',
            'gps_latitud' => '6.158420',
            'status_tanah' => 'Sendiri',
            'keluasan_tanah' => 7.50,
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 3,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Permanian Beradas',
            'pengakuan_benar' => true,
            'tarikh_permohonan' => date('Y-m-d'),
            'status_kelengkapan' => 'Dalam Semakan',
            'syor_permohonan' => 'Belum Disemak',
            'status_negeri' => 'Menunggu Kelulusan',
            'status_permohonan' => 'Dihantar',
        ], $overrides));

        LivestockInventory::create([
            'application_id' => $app->id,
            'baka' => 'CHAROLAIS',
            'betina_anak' => 2,
            'betina_dara' => 2,
            'betina_induk' => 5,
            'jantan_anak' => 1,
            'jantan_pejantan' => 1,
            'jumlah_baka' => 11,
        ]);

        return $app;
    }

    public function test_home_page_loads_successfully()
    {
        $response = $this->get(route('public.home'));
        $response->assertStatus(200);
        $response->assertSee('Program Ladang Bridlot');
        $response->assertSee('NAIMbif Kelantan');
    }

    public function test_apply_form_page_loads_successfully()
    {
        $response = $this->get(route('public.apply'));
        $response->assertStatus(200);
        $response->assertSee('BORANG PERMOHONAN PENYERTAAN LADANG BRIDLOT NAIMbif');
        $response->assertSee('MAKLUMAT PESERTA');
        $response->assertSee('STOK TERNAKAN');
    }

    public function test_applicant_can_submit_new_application()
    {
        $payload = [
            'nama' => 'Ahmad Daniel bin Rosli',
            'no_kp' => '900101035677',
            'no_telefon' => '012-3456789',
            'alamat_tetap' => 'No 12, Jalan Pasir Puteh',
            'poskod' => '16800',
            'jajahan' => 'Pasir Puteh',
            'pengalaman_menternak' => 5,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => '1',
            'nama_kursus' => 'Kursus Penternakan Bridlot Lembu',
            'anjuran_kursus' => 'JPV Kelantan',
            'alamat_ladang' => 'Lot 102, Kg Wakaf Berangan, Pasir Puteh',
            'poskod_ladang' => '16800',
            'jajahan_ladang' => 'Pasir Puteh',
            'gps_longitud' => '102.401200',
            'gps_latitud' => '5.834100',
            'status_tanah' => 'Sendiri',
            'keluasan_tanah' => 5.5,
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 2,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Permanian Beradas',
            'pengakuan_benar' => '1',
            'tarikh_permohonan' => date('Y-m-d'),
            'stok' => [
                'CHAROLAIS' => [
                    'betina_anak' => 2,
                    'betina_dara' => 3,
                    'betina_induk' => 5,
                    'jantan_anak' => 1,
                    'jantan_pejantan' => 1,
                ],
                'BELGIAN BLUE' => [
                    'betina_anak' => 0,
                    'betina_dara' => 0,
                    'betina_induk' => 0,
                    'jantan_anak' => 0,
                    'jantan_pejantan' => 0,
                ],
                "BLONDE D'AQUITAINE" => [
                    'betina_anak' => 0,
                    'betina_dara' => 0,
                    'betina_induk' => 0,
                    'jantan_anak' => 0,
                    'jantan_pejantan' => 0,
                ],
                'LIMOUSIN' => [
                    'betina_anak' => 1,
                    'betina_dara' => 2,
                    'betina_induk' => 3,
                    'jantan_anak' => 0,
                    'jantan_pejantan' => 1,
                ],
                'KEDAH KELANTAN' => [
                    'betina_anak' => 5,
                    'betina_dara' => 5,
                    'betina_induk' => 10,
                    'jantan_anak' => 2,
                    'jantan_pejantan' => 2,
                ],
                'LAIN-LAIN' => [
                    'nama_baka_lain' => 'Brahman',
                    'betina_anak' => 0,
                    'betina_dara' => 0,
                    'betina_induk' => 0,
                    'jantan_anak' => 0,
                    'jantan_pejantan' => 0,
                ],
            ],
        ];

        $response = $this->post(route('public.store'), $payload);

        $this->assertDatabaseHas('applications', [
            'nama' => 'AHMAD DANIEL BIN ROSLI',
            'no_kp' => '900101035677',
            'jajahan' => 'Pasir Puteh',
        ]);

        $app = Application::where('no_kp', '900101035677')->first();
        $this->assertNotNull($app);

        $response->assertRedirect(route('public.success', $app->no_rujukan));
    }

    public function test_applicant_can_check_status_via_ic_or_ref()
    {
        $app = $this->createTestApp();

        // Search by IC
        $response = $this->get(route('public.check_status', ['carian' => $app->no_kp]));
        $response->assertStatus(200);
        $response->assertSee($app->nama);
        $response->assertSee($app->no_rujukan);

        // Search by Ref No
        $response2 = $this->get(route('public.check_status', ['carian' => $app->no_rujukan]));
        $response2->assertStatus(200);
        $response2->assertSee($app->nama);
    }

    public function test_official_print_form_renders_authentic_layout()
    {
        $app = $this->createTestApp();

        $response = $this->get(route('public.print', $app->no_rujukan));
        $response->assertStatus(200);
        $response->assertSee('BORANG PERMOHONAN PENYERTAAN LADANG BRIDLOT NAIMbif');
        $response->assertSee('MAKLUMAT PESERTA');
        $response->assertSee('UNTUK KEGUNAAN PEJABAT (JAJAHAN)');
        $response->assertSee('ULASAN NEGERI');
        $response->assertSee($app->nama);
    }

    public function test_officer_can_login_and_access_dashboard()
    {
        $user = User::where('email', 'kb@jpvnk.gov.my')->first();

        $loginResponse = $this->post(route('login'), [
            'email' => 'kb@jpvnk.gov.my',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard Analitik NAIMbif');
    }

    public function test_jajahan_officer_can_update_inspection_and_premise_id()
    {
        $user = User::where('email', 'kb@jpvnk.gov.my')->first();
        $app = $this->createTestApp();

        $response = $this->actingAs($user)->post(route('admin.applications.update_jajahan', $app->id), [
            'id_premis' => 'JPV/KB/BL/2026/099',
            'status_kelengkapan' => 'Lengkap',
            'syor_permohonan' => 'Disokong',
            'pegawai_penyiasat' => 'En. Mohd Ridzuan bin Abdullah',
            'tarikh_siasatan' => date('Y-m-d'),
            'catatan_jajahan' => 'Premis lengkap dan menepati piawaian.',
        ]);

        $response->assertRedirect(route('admin.applications.show', $app->id));

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'id_premis' => 'JPV/KB/BL/2026/099',
            'status_kelengkapan' => 'Lengkap',
            'syor_permohonan' => 'Disokong',
        ]);
    }

    public function test_state_officer_cannot_approve_before_district_investigation()
    {
        $user = User::where('email', 'negeri@jpvnk.gov.my')->first();
        $app = $this->createTestApp(); // Initial status: syor_permohonan is 'Belum Disemak'

        // 1. Visit show page: approval form is locked / hidden, shows "Tindakan Kelulusan Belum Dibuka"
        $showResponse = $this->actingAs($user)->get(route('admin.applications.show', $app->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Tindakan Kelulusan Belum Dibuka');
        $showResponse->assertDontSee('Kemas Kini Keputusan Jabatan');

        // 2. Direct POST is blocked with error message
        $response = $this->actingAs($user)->post(route('admin.applications.update_negeri', $app->id), [
            'status_negeri' => 'Lulus',
            'no_rujukan_negeri' => 'JPVNK/BRIDLOT/2026/TEST01',
            'pegawai_pelulus' => 'Dr. Ahmad Farhan bin Ismail',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_state_officer_can_approve_after_district_investigation()
    {
        $user = User::where('email', 'negeri@jpvnk.gov.my')->first();
        $app = $this->createTestApp();

        // Simulate district officer completing investigation
        $app->update([
            'syor_permohonan' => 'Disokong',
            'status_kelengkapan' => 'Lengkap',
            'tarikh_semakan_jajahan' => now(),
            'pegawai_penyiasat' => 'En. Mohd Ridzuan bin Abdullah',
        ]);

        // 1. Visit show page: approval form is available
        $showResponse = $this->actingAs($user)->get(route('admin.applications.show', $app->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Kemas Kini Keputusan Jabatan');

        // 2. State officer submits approval
        $response = $this->actingAs($user)->post(route('admin.applications.update_negeri', $app->id), [
            'status_negeri' => 'Lulus',
            'no_rujukan_negeri' => 'JPVNK/BRIDLOT/2026/TEST01',
            'pegawai_pelulus' => 'Dr. Ahmad Farhan bin Ismail',
            'ulasan_negeri' => 'Diluluskan untuk menerima bantuan pakej baka.',
        ]);

        $response->assertRedirect(route('admin.applications.show', $app->id));

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status_negeri' => 'Lulus',
            'no_rujukan_negeri' => 'JPVNK/BRIDLOT/2026/TEST01',
        ]);
    }

    public function test_district_officer_cannot_update_state_decision()
    {
        $districtOfficer = User::where('email', 'kb@jpvnk.gov.my')->first();
        $app = $this->createTestApp();

        // 1. Visit show page: District officer should NOT see the state decision submit button
        $showResponse = $this->actingAs($districtOfficer)->get(route('admin.applications.show', $app->id));
        $showResponse->assertStatus(200);
        $showResponse->assertDontSee('Kemas Kini Keputusan Jabatan');
        $showResponse->assertSee('Tindakan Pejabat Jajahan');
        $showResponse->assertSee('Keputusan Ibu Pejabat JPVNK');

        // 2. Direct POST to update_negeri is blocked
        $postResponse = $this->actingAs($districtOfficer)->post(route('admin.applications.update_negeri', $app->id), [
            'status_negeri' => 'Lulus',
            'pegawai_pelulus' => 'En. Mohd Ridzuan bin Abdullah',
        ]);

        $postResponse->assertSessionHas('error');
    }

    public function test_state_officer_cannot_update_district_inspection()
    {
        $stateOfficer = User::where('email', 'negeri@jpvnk.gov.my')->first();
        $app = $this->createTestApp();

        // 1. Visit show page: State officer should NOT see the district submit button
        $showResponse = $this->actingAs($stateOfficer)->get(route('admin.applications.show', $app->id));
        $showResponse->assertStatus(200);
        $showResponse->assertDontSee('Simpan Ulasan Jajahan');
        $showResponse->assertSee('Laporan Siasatan Jajahan');
        $showResponse->assertSee('Tindakan Kelulusan Belum Dibuka');

        // 2. Direct POST to update_jajahan is blocked
        $postResponse = $this->actingAs($stateOfficer)->post(route('admin.applications.update_jajahan', $app->id), [
            'id_premis' => 'JPV/TEST/2026/01',
            'status_kelengkapan' => 'Lengkap',
            'syor_permohonan' => 'Disokong',
            'pegawai_penyiasat' => 'Dr. State Officer',
        ]);

        $postResponse->assertSessionHas('error');
    }

    public function test_admin_can_export_applications_to_csv()
    {
        $user = User::where('email', 'admin@jpvnk.gov.my')->first();

        $response = $this->actingAs($user)->get(route('admin.applications.export'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_applicant_must_verify_2fa_to_access_edit_form()
    {
        $app = $this->createTestApp();

        // 1. Direct access without session verification triggers security challenge
        $response = $this->get(route('public.edit', $app->no_rujukan));
        $response->assertStatus(200);
        $response->assertSee('Pengesahan Keselamatan Dwi-Faktor');
        $response->assertSee('No. Kad Pengenalan Pemohon');
        $response->assertSee('No. Telefon');

        // 2. Submit wrong IC + correct Phone -> rejected
        $wrongIcResponse = $this->post(route('public.verify_edit', $app->no_rujukan), [
            'no_kp' => '999999999999',
            'no_telefon' => $app->no_telefon,
        ]);
        $wrongIcResponse->assertSessionHas('error');

        // 3. Submit correct IC + wrong Phone -> rejected
        $wrongPhoneResponse = $this->post(route('public.verify_edit', $app->no_rujukan), [
            'no_kp' => $app->no_kp,
            'no_telefon' => '012-0000000',
        ]);
        $wrongPhoneResponse->assertSessionHas('error');

        // 4. Submit correct IC + correct full Phone -> approved
        $correctResponse = $this->post(route('public.verify_edit', $app->no_rujukan), [
            'no_kp' => $app->no_kp,
            'no_telefon' => $app->no_telefon,
        ]);
        $correctResponse->assertRedirect(route('public.edit', $app->no_rujukan));

        // 5. Submit correct IC + correct last 4 digits of Phone -> approved
        $cleanPhone = preg_replace('/[^0-9]/', '', $app->no_telefon);
        $last4 = substr($cleanPhone, -4);
        $correct4DigitResponse = $this->post(route('public.verify_edit', $app->no_rujukan), [
            'no_kp' => $app->no_kp,
            'no_telefon' => $last4,
        ]);
        $correct4DigitResponse->assertRedirect(route('public.edit', $app->no_rujukan));

        // 6. Now with verified session, edit form is accessible
        $editFormResponse = $this->withSession(["verified_applicant_{$app->no_rujukan}" => $app->no_kp])
            ->get(route('public.edit', $app->no_rujukan));
        $editFormResponse->assertStatus(200);
        $editFormResponse->assertSee('SIMPAN PERUBAHAN PERMOHONAN');
        $editFormResponse->assertSee($app->nama);

        // 7. Direct GET request on verify_edit route does not throw 405, but redirects gracefully
        $directGetResponse = $this->get(route('public.verify_edit', $app->no_rujukan));
        $directGetResponse->assertRedirect(route('public.edit', $app->no_rujukan));

        // 8. Checking status with IC does not bypass 2FA requirement
        $statusResponse = $this->get(route('public.check_status', ['carian' => $app->no_kp]));
        $statusResponse->assertStatus(200);
        $editAfterStatusResponse = $this->get(route('public.edit', $app->no_rujukan));
        $editAfterStatusResponse->assertStatus(200);
        $editAfterStatusResponse->assertSee('Pengesahan Keselamatan Dwi-Faktor');
    }

    public function test_applicant_can_update_application_data()
    {
        $app = $this->createTestApp();

        $updatePayload = [
            'nama' => 'NIK MOHD AZLAN BIN NIK OTHMAN (KEMASKINI)',
            'no_kp' => $app->no_kp,
            'no_telefon' => '019-9998888',
            'alamat_tetap' => 'PT 999 Kampung Tok Uban',
            'poskod' => '17000',
            'jajahan' => 'Pasir Mas',
            'pengalaman_menternak' => 8,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => '1',
            'nama_kursus' => 'Kursus Lanjutan Bridlot',
            'anjuran_kursus' => 'JPVNK',
            'status_tanah' => 'Sendiri',
            'keluasan_tanah' => 5.5,
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 3,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Permanian Beradas',
            'stok' => [
                'CHAROLAIS' => [
                    'betina_anak' => 2,
                    'betina_dara' => 3,
                    'betina_induk' => 5,
                    'jantan_anak' => 1,
                    'jantan_pejantan' => 2,
                ],
                "BLONDE D'AQUITAINE" => [
                    'betina_anak' => 4,
                    'betina_dara' => 1,
                    'betina_induk' => 2,
                    'jantan_anak' => 0,
                    'jantan_pejantan' => 1,
                ],
            ],
        ];

        $response = $this->withSession(["verified_applicant_{$app->no_rujukan}" => $app->no_kp])
            ->put(route('public.update', $app->no_rujukan), $updatePayload);

        $response->assertRedirect(route('public.check_status', ['carian' => $app->no_rujukan]));

        $this->assertDatabaseHas('livestock_inventories', [
            'application_id' => $app->id,
            'baka' => "BLONDE D'AQUITAINE",
            'betina_anak' => 4,
            'jumlah_baka' => 8,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'nama' => 'NIK MOHD AZLAN BIN NIK OTHMAN (KEMASKINI)',
            'no_telefon' => '019-9998888',
            'keluasan_tanah' => 5.5,
        ]);
    }

    public function test_approved_application_can_be_edited_by_applicant()
    {
        $app = $this->createTestApp();
        $app->update([
            'status_negeri' => 'Lulus',
            'no_rujukan_negeri' => 'JPVNK/L/2026/001',
        ]);

        // 1. Can view edit form
        $response = $this->withSession(["verified_applicant_{$app->no_rujukan}" => $app->no_kp])
            ->get(route('public.edit', $app->no_rujukan));
        $response->assertStatus(200);
        $response->assertSee('Permohonan Ini Telah Diluluskan Rasmi');

        // 2. Can submit update to approved application
        $updatePayload = [
            'nama' => 'NIK MOHD AZLAN (PENTERNAK LULUS)',
            'no_kp' => $app->no_kp,
            'no_telefon' => '019-9112233',
            'alamat_tetap' => 'PT 123 Kg Baru',
            'poskod' => '16100',
            'jajahan' => 'Kota Bharu',
            'pengalaman_menternak' => 10,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => '0',
            'berminat_kursus_jpvnk' => '1',
            'status_tanah' => 'Sendiri',
            'keluasan_tanah' => 8.0,
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 4,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'stok' => [
                'CHAROLAIS' => ['betina_anak' => 5, 'betina_dara' => 2, 'betina_induk' => 3, 'jantan_anak' => 1, 'jantan_pejantan' => 1],
            ],
        ];

        $updateResponse = $this->withSession(["verified_applicant_{$app->no_rujukan}" => $app->no_kp])
            ->put(route('public.update', $app->no_rujukan), $updatePayload);

        $updateResponse->assertRedirect(route('public.check_status', ['carian' => $app->no_rujukan]));

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'nama' => 'NIK MOHD AZLAN (PENTERNAK LULUS)',
            'keluasan_tanah' => 8.0,
            'status_negeri' => 'Lulus',
            'no_rujukan_negeri' => 'JPVNK/L/2026/001',
        ]);
    }

    public function test_submitting_application_creates_system_notification()
    {
        $app = $this->createTestApp();

        \App\Models\SystemNotification::logAndNotify(
            type: 'permohonan_baru',
            title: 'Permohonan Baru Diterima (' . $app->no_rujukan . ')',
            message: 'Permohonan baru oleh ' . $app->nama,
            application: $app,
            actionUrl: route('admin.applications.show', $app->id)
        );

        $this->assertDatabaseHas('system_notifications', [
            'type' => 'permohonan_baru',
            'no_rujukan' => $app->no_rujukan,
            'is_read' => false,
        ]);
    }

    public function test_officer_can_view_notifications_and_mark_as_read()
    {
        $user = User::where('email', 'admin@jpvnk.gov.my')->first();
        $app = $this->createTestApp();

        $notif = \App\Models\SystemNotification::create([
            'application_id' => $app->id,
            'no_rujukan' => $app->no_rujukan,
            'type' => 'permohonan_baru',
            'title' => 'Permohonan Baru Demo',
            'message' => 'Ujian notifikasi sistem',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Permohonan Baru Demo');

        // Mark as read
        $markResponse = $this->actingAs($user)->get(route('admin.notifications.show', $notif->id));
        $this->assertDatabaseHas('system_notifications', [
            'id' => $notif->id,
            'is_read' => true,
        ]);

        // Mark all
        $markAllResponse = $this->actingAs($user)->post(route('admin.notifications.mark_all'));
        $markAllResponse->assertRedirect();
    }

    public function test_notifications_endpoint_returns_json()
    {
        $user = User::where('email', 'admin@jpvnk.gov.my')->first();
        $response = $this->actingAs($user)->get(route('admin.notifications.latest'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unread_count',
            'notifications',
        ]);
    }

    public function test_admin_activities_are_not_logged_to_notifications()
    {
        $admin = User::where('email', 'admin@jpvnk.gov.my')->first();
        $officer = User::where('email', 'kb@jpvnk.gov.my')->first();
        $app = $this->createTestApp();
        $initialCount = \App\Models\SystemNotification::count();

        // 1. Admin logs notification via helper -> ignored (returns null, no db insert)
        $this->actingAs($admin);
        $result = \App\Models\SystemNotification::logAndNotify(
            type: 'ulasan_jajahan',
            title: 'Tindakan Admin Ujian',
            message: 'Aktiviti oleh Admin tidak perlu masuk notifikasi',
            application: $app
        );

        $this->assertNull($result);
        $this->assertEquals($initialCount, \App\Models\SystemNotification::count());

        // 2. Non-admin (District Officer) triggers notification -> successfully created
        $this->actingAs($officer);
        $officerResult = \App\Models\SystemNotification::logAndNotify(
            type: 'ulasan_jajahan',
            title: 'Tindakan Pegawai Jajahan',
            message: 'Aktiviti oleh Pegawai Jajahan direkodkan',
            application: $app
        );

        $this->assertNotNull($officerResult);
        $this->assertEquals($initialCount + 1, \App\Models\SystemNotification::count());
    }

    public function test_officer_can_view_registration_form()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Akaun Pegawai');
        $response->assertSee('Alamat E-mel Rasmi');
    }

    public function test_officer_can_register_new_account()
    {
        $payload = [
            'name' => 'Dr. Wan Mohd Hafiz bin Wan Harun',
            'email' => 'hafiz.harun@jpvnk.gov.my',
            'jawatan' => 'Pegawai Veterinar GV44',
            'no_telefon' => '019-9112233',
            'role' => 'pegawai_jajahan',
            'jajahan' => 'Bachok',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $payload);
        $response->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'hafiz.harun@jpvnk.gov.my',
            'jajahan' => 'Bachok',
            'role' => 'pegawai_jajahan',
        ]);
    }

    public function test_admin_can_view_and_manage_officers()
    {
        $admin = User::where('email', 'admin@jpvnk.gov.my')->first();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengurusan Akaun Pegawai');

        // Admin create new officer directly
        $createResponse = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Pn. Halimah binti Kassim',
            'email' => 'halimah@jpvnk.gov.my',
            'jawatan' => 'Pembantu Veterinar G19',
            'no_telefon' => '012-3456789',
            'role' => 'pegawai_jajahan',
            'jajahan' => 'Tumpat',
            'password' => 'password',
        ]);
        $createResponse->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'halimah@jpvnk.gov.my',
            'jajahan' => 'Tumpat',
        ]);
    }

    public function test_admin_can_update_officer_details()
    {
        $admin = User::where('email', 'admin@jpvnk.gov.my')->first();
        $targetUser = User::where('email', 'kb@jpvnk.gov.my')->first();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser->id), [
            'name' => 'En. Mohd Ridzuan bin Abdullah (Senior)',
            'email' => $targetUser->email,
            'jawatan' => 'Ketua Pegawai Veterinar Jajahan',
            'no_telefon' => '019-9876543',
            'role' => 'pegawai_jajahan',
            'jajahan' => 'Kota Bharu',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'En. Mohd Ridzuan bin Abdullah (Senior)',
            'jawatan' => 'Ketua Pegawai Veterinar Jajahan',
        ]);
    }

    public function test_duplicate_active_ic_submission_is_prevented()
    {
        $existing = $this->createTestApp(); // IC: 850712035411

        $payload = [
            'nama' => 'WAN MUHAMMAD AZLAN BIN WAN HASSAN',
            'no_kp' => '850712-03-5411',
            'no_telefon' => '019-9112233',
            'alamat_tetap' => 'Kg. Padang Tembak, Pengkalan Chepa',
            'poskod' => '16100',
            'jajahan' => 'Kota Bharu',
            'pengalaman_menternak' => 5,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => '0',
            'berminat_kursus_jpvnk' => '1',
            'status_tanah' => 'Sendiri',
            'keluasan_tanah' => 4.5,
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 2,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'pengakuan_benar' => '1',
            'tarikh_permohonan' => date('Y-m-d'),
            'stok' => [
                'CHAROLAIS' => ['betina_anak' => 2, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0]
            ]
        ];

        $response = $this->post(route('public.store'), $payload);
        $response->assertSessionHas('duplicate_error');

        // Total applications with this IC should still be 1 (not duplicated)
        $count = Application::where('no_kp', '850712035411')->count();
        $this->assertEquals(1, $count);
    }

    public function test_applicant_with_failed_previous_application_can_reapply()
    {
        $failedApp = $this->createTestApp();
        $failedApp->update([
            'no_kp' => '990101039999',
            'status_negeri' => 'Gagal',
        ]);

        $payload = [
            'nama' => 'PENTERNAK BAHARU REAPPLY',
            'no_kp' => '990101-03-9999',
            'no_telefon' => '019-9998877',
            'alamat_tetap' => 'Kg. Baru, Pasir Mas',
            'poskod' => '17000',
            'jajahan' => 'Pasir Mas',
            'pengalaman_menternak' => 3,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => '0',
            'berminat_kursus_jpvnk' => '1',
            'status_tanah' => 'Sendiri',
            'keluasan_tanah' => 3.0,
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 1,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'pengakuan_benar' => '1',
            'tarikh_permohonan' => date('Y-m-d'),
            'stok' => [
                'CHAROLAIS' => ['betina_anak' => 1, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0]
            ]
        ];

        $response = $this->post(route('public.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionMissing('duplicate_error');

        $this->assertEquals(2, Application::where('no_kp', '990101039999')->count());
    }

    public function test_real_time_ic_lookup_api()
    {
        $app = $this->createTestApp(); // IC: 850712035411

        // 1. Existing IC returns exists: true
        $response = $this->getJson(route('public.check_ic', ['no_kp' => '850712-03-5411']));
        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
            'no_rujukan' => $app->no_rujukan,
        ]);

        // 2. Non-existing IC returns exists: false
        $responseNon = $this->getJson(route('public.check_ic', ['no_kp' => '700101031111']));
        $responseNon->assertStatus(200);
        $responseNon->assertJson([
            'exists' => false,
        ]);
    }
}


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('no_rujukan')->unique(); // e.g. NAIMBIF-2026-0001

            // 1. MAKLUMAT PESERTA
            $table->string('nama');
            $table->string('no_kp', 20)->index(); // No Kad Pengenalan
            $table->string('no_telefon', 30);
            $table->text('alamat_tetap');
            $table->string('poskod', 10);
            $table->string('jajahan', 50)->index(); // Kota Bharu, Pasir Mas, etc.
            $table->integer('pengalaman_menternak')->default(0); // Tahun
            $table->string('status_penternakan')->default('Sepenuh Masa'); // Sepenuh Masa / Sampingan
            $table->boolean('pernah_kursus')->default(false); // Ya / Tidak
            $table->string('nama_kursus')->nullable();
            $table->string('anjuran_kursus')->nullable();
            $table->boolean('berminat_kursus_jpvnk')->nullable(); // Jika tidak pernah kursus: Ya / Tidak

            // 2. MAKLUMAT ASAS LADANG
            $table->text('alamat_ladang')->nullable(); // Jika berlainan dari alamat di atas
            $table->string('poskod_ladang', 10)->nullable();
            $table->string('jajahan_ladang', 50)->nullable();
            $table->string('gps_longitud', 50)->nullable(); // Longitud [E]
            $table->string('gps_latitud', 50)->nullable(); // Latitud [N]
            $table->string('status_tanah')->default('Sendiri'); // Sendiri / Sewa / Kerajaan / Lain-lain
            $table->string('status_tanah_lain')->nullable();
            $table->decimal('keluasan_tanah', 10, 2)->default(0); // Ekar
            $table->string('padang_ragut')->default('Ada'); // Ada / Tiada
            $table->integer('bilangan_pekerja')->default(0); // Orang

            // 3. MAKLUMAT ASAS TERNAKAN
            $table->string('punca_ternakan')->default('Beli'); // Beli / Pawah / Lain-lain
            $table->string('punca_ternakan_lain')->nullable();
            $table->string('kaedah_pembiakan')->default('Asli'); // Asli / Permanian Beradas

            // 4. PENGAKUAN PEMOHON
            $table->boolean('pengakuan_benar')->default(true);
            $table->longText('tandatangan')->nullable(); // Data URL Base64 or Image path
            $table->date('tarikh_permohonan')->useCurrent();

            // 5. UNTUK KEGUNAAN PEJABAT (JAJAHAN)
            $table->string('id_premis')->nullable();
            $table->string('status_kelengkapan')->default('Dalam Semakan'); // Lengkap / Tidak Lengkap / Dalam Semakan
            $table->string('syor_permohonan')->default('Belum Disemak'); // Disokong / Tidak disokong / Belum Disemak
            $table->string('pegawai_penyiasat')->nullable();
            $table->date('tarikh_siasatan')->nullable();
            $table->text('catatan_jajahan')->nullable();
            $table->timestamp('tarikh_semakan_jajahan')->nullable();
            $table->foreignId('disemak_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();

            // 6. ULASAN NEGERI (IBU PEJABAT JPVNK)
            $table->string('status_negeri')->default('Menunggu Kelulusan'); // Lulus / Gagal / Menunggu Kelulusan
            $table->string('no_rujukan_negeri')->nullable(); // No Rujukan Kelulusan
            $table->text('ulasan_negeri')->nullable();
            $table->string('pegawai_pelulus')->nullable();
            $table->timestamp('tarikh_kelulusan_negeri')->nullable();
            $table->foreignId('diluluskan_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();

            // STATUS KESELURUHAN
            $table->string('status_permohonan')->default('Dihantar')->index(); 
            // Dihantar -> Disemak Jajahan -> Lulus / Gagal

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

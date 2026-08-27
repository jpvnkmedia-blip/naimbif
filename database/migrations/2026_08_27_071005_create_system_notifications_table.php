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
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->string('no_rujukan')->nullable()->index();
            $table->string('jajahan')->nullable()->index();
            $table->string('type')->index(); // permohonan_baru, permohonan_dikemaskini, ulasan_jajahan, keputusan_jabatan
            $table->string('title');
            $table->text('message');
            $table->string('icon')->default('fas fa-bell');
            $table->string('badge_color')->default('emerald'); // emerald, blue, amber, purple, rose
            $table->string('action_url')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};

<?php

use App\Http\Controllers\Admin\ApplicationManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicApplicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Portal Awam NAIMbif JPVNK)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicApplicationController::class, 'index'])->name('public.home');
Route::get('/permohonan', [PublicApplicationController::class, 'create'])->name('public.apply');
Route::post('/permohonan/simpan', [PublicApplicationController::class, 'store'])->name('public.store');
Route::get('/permohonan/kemaskini/{no_rujukan}', [PublicApplicationController::class, 'edit'])->name('public.edit');
Route::post('/permohonan/sahkan/{no_rujukan}', [PublicApplicationController::class, 'verifyEdit'])->name('public.verify_edit');
Route::put('/permohonan/kemaskini/{no_rujukan}', [PublicApplicationController::class, 'update'])->name('public.update');
Route::get('/permohonan/berjaya/{no_rujukan}', [PublicApplicationController::class, 'success'])->name('public.success');
Route::match(['get', 'post'], '/semakan', [PublicApplicationController::class, 'checkStatus'])->name('public.check_status');
Route::get('/cetak/{no_rujukan}', [PublicApplicationController::class, 'printForm'])->name('public.print');
Route::get('/api/semak-kp', [PublicApplicationController::class, 'checkExistingIc'])->name('public.check_ic');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Log Masuk & Pendaftaran Pegawai)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Officer & Admin Routes (Modul Pentadbiran & Semakan)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Senarai & Urus Permohonan
    Route::get('/permohonan', [ApplicationManagementController::class, 'index'])->name('applications.index');
    Route::get('/permohonan/eksport', [ApplicationManagementController::class, 'exportCsv'])->name('applications.export');
    Route::get('/permohonan/{id}', [ApplicationManagementController::class, 'show'])->name('applications.show');
    Route::post('/permohonan/{id}/jajahan', [ApplicationManagementController::class, 'updateJajahan'])->name('applications.update_jajahan');
    Route::post('/permohonan/{id}/negeri', [ApplicationManagementController::class, 'updateNegeri'])->name('applications.update_negeri');
    Route::delete('/permohonan/{id}', [ApplicationManagementController::class, 'destroy'])->name('applications.destroy');

    // Notifikasi & Log Aktiviti Sistem
    Route::get('/notifikasi', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/terkini', [\App\Http\Controllers\Admin\NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::get('/notifikasi/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.show');
    Route::post('/notifikasi/baca-semua', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all');

    // Pengurusan Akaun Pegawai (Admin / Pentadbir)
    Route::get('/pegawai', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/pegawai', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::put('/pegawai/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/pegawai/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
});

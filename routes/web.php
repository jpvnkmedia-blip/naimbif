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
Route::get('/permohonan/berjaya/{no_rujukan}', [PublicApplicationController::class, 'success'])->name('public.success');
Route::match(['get', 'post'], '/semakan', [PublicApplicationController::class, 'checkStatus'])->name('public.check_status');
Route::get('/cetak/{no_rujukan}', [PublicApplicationController::class, 'printForm'])->name('public.print');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Log Masuk Pegawai)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
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
});

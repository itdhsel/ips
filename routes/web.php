<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ConsultingController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SsoController;

// 1. Catch unauthenticated users (The 'auth' middleware automatically redirects here)
Route::get('/login', function () {
    return redirect('http://hsel-sso.ddev.site/login'); 
})->name('login');

// 2. SSO Callback matching central SSO server redirect URL
Route::get('/imonitor/sso/callback', [SsoController::class, 'handleCallback']);

// 3. Protected Application Routes (STRICTLY FOR LOGGED IN USERS)
Route::middleware(['auth', 'single.session'])->group(function () {
    
    // Live Corporate Dashboard
    Route::get('/', [MonitorController::class, 'dashboard'])->name('dashboard');

    // iMonitor: Discharge Medications Status Route
    Route::get('/ips', [MonitorController::class, 'index'])->name('monitor.index');

    // iMonitor: Counselling Request Routes
    Route::get('/counselling', [ConsultingController::class, 'index'])->name('counselling.index');
    Route::post('/counselling', [ConsultingController::class, 'store'])->name('counselling.store');

    // iMonitor: Discharge Medication Collection Routes
    Route::get('/collection', [CollectionController::class, 'index'])->name('collection.index');
    Route::post('/collection/update', [CollectionController::class, 'store'])->name('collection.store');

    // iMonitor: Reporting Submodule
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // API Routes
    Route::get('/api/search-mrn', [MonitorController::class, 'searchMrn'])->name('api.search.mrn');

    // Restrict Store & Update to Admin and Pharmacy staff
    Route::middleware(['role:admin,pharmacy'])->group(function () {
        Route::post('/ips/store', [MonitorController::class, 'store'])->name('monitor.store');
        Route::post('/ips/update/{id}', [MonitorController::class, 'update'])->name('monitor.update');
    });

    // Restrict Delete and Counselling List strictly to Admin level
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/ips/delete/{id}', [MonitorController::class, 'destroy'])->name('monitor.destroy');
        Route::get('/counselling/list', [ConsultingController::class, 'list'])->name('counselling.list');
    });

});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ConsultingController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SsoController;

// 1. Keep SSO Callback matching central SSO server redirect URL
Route::get('/imonitor/sso/callback', [SsoController::class, 'handleCallback']);

// Protected Application Routes (STRICTLY FOR LOGGED IN USERS)
Route::middleware(['auth', 'single.session'])->group(function () {
    
    // iMonitor: Discharge Medications Status Route
    Route::get('/ips', [MonitorController::class, 'index'])->name('monitor.index');

    // iMonitor: Counselling Request Routes
    Route::get('/counselling', [ConsultingController::class, 'index'])->name('counselling.index');
    Route::post('/counselling', [ConsultingController::class, 'store'])->name('counselling.store');

    // iMonitor: Discharge Medication Collection Routes
    Route::get('/collection', [CollectionController::class, 'index'])->name('collection.index');
    Route::post('/collection/update', [CollectionController::class, 'store'])->name('collection.store');

    // API Routes
    Route::get('/api/search-mrn', [MonitorController::class, 'searchMrn'])->name('api.search.mrn');

    // Restrict Store & Update to Admin and Pharmacy staff
    Route::middleware(['role:admin,pharmacy'])->group(function () {
        Route::post('/ips/store', [MonitorController::class, 'store'])->name('monitor.store');
        Route::post('/ips/update/{id}', [MonitorController::class, 'update'])->name('monitor.update');
    });

    // Restrict Delete strictly to Admin level
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/ips/delete/{id}', [MonitorController::class, 'destroy'])->name('monitor.destroy');
    });
});

// 2. Catch unauthenticated users (PUBLIC - OUTSIDE THE MIDDLEWARE)
Route::get('/', function () {
    // Redirects the user back to your SSO login page
    return redirect('http://hsel-sso.ddev.site/login'); 
})->name('login');
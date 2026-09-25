<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ConsultingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EcdrController;
use App\Http\Controllers\PatientApiController;
use App\Http\Controllers\SsoController;

// 1. Catch unauthenticated users
Route::get('/login', function () {
    return redirect('http://hsel-sso.ddev.site/login'); 
})->name('login');

// 2. SSO Callback
Route::get('/imonitor/sso/callback', [SsoController::class, 'handleCallback']);

// 3. Protected Application Routes
Route::middleware(['auth', 'single.session'])->group(function () {
    
    // Default Route: Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // iMonitor: Discharge Medications Status Route (Changed from /ips to /status)
    Route::get('/status', [MonitorController::class, 'index'])->name('monitor.index');

    // iMonitor: Counselling Request Routes
    Route::get('/api/search-counselling-mrn', [\App\Http\Controllers\PatientApiController::class, 'searchCounsellingMrn']);
    Route::get('/counselling', [ConsultingController::class, 'index'])->name('counselling.index');
    Route::post('/counselling', [ConsultingController::class, 'store'])->name('counselling.store');
    Route::post('/counselling/update/{id}', [ConsultingController::class, 'update'])->name('counselling.update');

    // iMonitor: Discharge Medication Collection Routes
    Route::get('/collection', [CollectionController::class, 'index'])->name('collection.index');
    Route::post('/collection/update', [CollectionController::class, 'store'])->name('collection.store');

    // iMonitor: Reporting Submodule
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// API Routes
Route::get('/api/search-mrn', [PatientApiController::class, 'searchImonitorMrn'])->name('api.search.mrn');
Route::get('/api/search-mrn/ecdr', [PatientApiController::class, 'searchEcdrMrn'])->name('api.search.mrn.ecdr');

// Restrict Update to Admin and Pharmacy staff
Route::middleware(['role:admin,pharmacy'])->group(function () {
    Route::post('/status/update/{id}', [MonitorController::class, 'update'])->name('monitor.update');
});

// Restrict New Order, Delete, and Counselling List strictly to Admin level
Route::middleware(['role:admin'])->group(function () {
    // Only Admins can access the form and submit new orders
    Route::get('/status/new-order', [MonitorController::class, 'create'])->name('monitor.create');
    Route::post('/status/store', [MonitorController::class, 'store'])->name('monitor.store');
    
    // Admin-only actions and lists
    Route::delete('/status/delete/{id}', [MonitorController::class, 'destroy'])->name('monitor.destroy');
    Route::get('/counselling/list', [ConsultingController::class, 'list'])->name('counselling.list');
});

// eCDR Submodules
Route::get('/ecdr/create', [EcdrController::class, 'create'])->name('ecdr.create');
Route::get('/ecdr/ward-list', [EcdrController::class, 'wardList'])->name('ecdr.ward_list');
Route::get('/ecdr/history', [EcdrController::class, 'history'])->name('ecdr.history');

// eCDR Actions
Route::post('/ecdr/store', [EcdrController::class, 'store'])->name('ecdr.store');
Route::get('/ecdr/show/{id}', [EcdrController::class, 'show'])->name('ecdr.show');
Route::post('/ecdr/cancel/{id}', [EcdrController::class, 'cancel'])->name('ecdr.cancel');
Route::get('/ecdr/view/{id}', [\App\Http\Controllers\EcdrController::class, 'showOrder'])->name('ecdr.showOrder');
Route::post('/ecdr/cancel/{id}', [\App\Http\Controllers\EcdrController::class, 'cancel'])->name('ecdr.cancel');
Route::get('/ecdr/edit/{id}', [\App\Http\Controllers\EcdrController::class, 'edit'])->name('ecdr.edit');
Route::post('/ecdr/update/{id}', [\App\Http\Controllers\EcdrController::class, 'update'])->name('ecdr.update');
});
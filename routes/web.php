<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Desa\VillageApbdesController;
use App\Http\Controllers\Desa\VillageAssetController as DesaAssetController;
use App\Http\Controllers\Desa\VillageDisbursementController;
use App\Http\Controllers\Desa\VillageHeadDashboardController;
use App\Http\Controllers\Desa\VillageProfileController;
use App\Http\Controllers\Desa\VillageReportController;
use App\Http\Controllers\Desa\VillageTransactionController;
use App\Http\Controllers\Pimpinan\LeaderDashboardController;
use App\Http\Controllers\Staff\ApbdesController;
use App\Http\Controllers\Staff\BudgetRealizationController;
use App\Http\Controllers\Staff\DisbursementVerificationController;
use App\Http\Controllers\Staff\ExpenditureController;
use App\Http\Controllers\Staff\ReceiptController;
use App\Http\Controllers\Staff\ReportController;
use App\Http\Controllers\Staff\SettingController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\UserController;
use App\Http\Controllers\Staff\VillageAssetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMKeuDesa
|--------------------------------------------------------------------------
*/

// Home & Authentication
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('staff.dashboard'),
            'pimpinan' => redirect()->route('pimpinan.dashboard'),
            'kepala_desa' => redirect()->route('desa.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 1. Regional Government Staff & Admin
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('staff')->as('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Disbursement verification
    Route::get('/disbursements', [DisbursementVerificationController::class, 'index'])->name('disbursements.index');
    Route::get('/disbursements/{id}', [DisbursementVerificationController::class, 'show'])->name('disbursements.show');
    Route::post('/disbursements/{id}/verify', [DisbursementVerificationController::class, 'verify'])->name('disbursements.verify');
    Route::get('/disbursements/document/{id}/download', [DisbursementVerificationController::class, 'downloadDocument'])->name('disbursements.download');

    // APBDes
    Route::resource('apbdes', ApbdesController::class);
    Route::post('/apbdes/{id}/status', [ApbdesController::class, 'verifyStatus'])->name('apbdes.status');

    // Budget Realization
    Route::get('/realization', [BudgetRealizationController::class, 'index'])->name('realization.index');

    // Receipts
    Route::resource('receipts', ReceiptController::class);

    // Expenditures
    Route::resource('expenditures', ExpenditureController::class);

    // Village Assets
    Route::resource('assets', VillageAssetController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

    // User Management
    Route::resource('users', UserController::class);
    Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/institution', [SettingController::class, 'updateInstitution'])->name('settings.institution');
    Route::post('/settings/villages', [SettingController::class, 'storeVillage'])->name('settings.village');
});

// ==========================================
// 2. Village Head (Kepala Desa)
// ==========================================
Route::middleware(['auth', 'role:kepala_desa'])->prefix('desa')->as('desa.')->group(function () {
    Route::get('/dashboard', [VillageHeadDashboardController::class, 'index'])->name('dashboard');

    // Fund disbursement documents
    Route::get('/disbursements', [VillageDisbursementController::class, 'index'])->name('disbursements.index');
    Route::get('/disbursements/create', [VillageDisbursementController::class, 'create'])->name('disbursements.create');
    Route::post('/disbursements', [VillageDisbursementController::class, 'store'])->name('disbursements.store');
    Route::get('/disbursements/{id}', [VillageDisbursementController::class, 'show'])->name('disbursements.show');
    Route::post('/disbursements/document/{docId}/reupload', [VillageDisbursementController::class, 'reupload'])->name('disbursements.reupload');

    // APBDes
    Route::get('/apbdes', [VillageApbdesController::class, 'index'])->name('apbdes');

    // Transactions: Receipts & Expenditures
    Route::get('/receipts', [VillageTransactionController::class, 'receipts'])->name('receipts');
    Route::get('/receipts/create', [VillageTransactionController::class, 'createReceipt'])->name('receipts.create');
    Route::post('/receipts', [VillageTransactionController::class, 'storeReceipt'])->name('receipts.store');

    Route::get('/expenditures', [VillageTransactionController::class, 'expenditures'])->name('expenditures');
    Route::get('/expenditures/create', [VillageTransactionController::class, 'createExpenditure'])->name('expenditures.create');
    Route::post('/expenditures', [VillageTransactionController::class, 'storeExpenditure'])->name('expenditures.store');

    // Village Assets
    Route::get('/assets', [DesaAssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [DesaAssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [DesaAssetController::class, 'store'])->name('assets.store');

    // Reports
    Route::get('/reports', [VillageReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [VillageReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/excel', [VillageReportController::class, 'exportExcel'])->name('reports.excel');

    // Profile & Password
    Route::get('/profile', [VillageProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [VillageProfileController::class, 'update'])->name('profile.update');
});

// ==========================================
// 3. Regional Government Leaders (Pimpinan Pemda)
// ==========================================
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->as('pimpinan.')->group(function () {
    Route::get('/dashboard', [LeaderDashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions', [LeaderDashboardController::class, 'transactions'])->name('transactions');
    Route::get('/monitoring', [LeaderDashboardController::class, 'monitoring'])->name('monitoring');

    // Read-only monitoring of disbursements
    Route::get('/disbursements', [DisbursementVerificationController::class, 'index'])->name('disbursements.index');
    Route::get('/disbursements/{id}', [DisbursementVerificationController::class, 'show'])->name('disbursements.show');

    // APBDes view
    Route::get('/apbdes/{id}', [ApbdesController::class, 'show'])->name('apbdes.show');

    // Consolidated reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
});

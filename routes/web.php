<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionReturnController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ManagementHubController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DebtController;

// Rute Publik (Landing Page)
Route::get('/', function () {
    return view('pages.landing'); 
})->name('landing');

// ----------------------------------------------------
// AREA TERKUNCI (HANYA BISA DIAKSES JIKA SUDAH LOGIN)
// ----------------------------------------------------
Route::middleware('auth')->group(function () {
    
    // 1. RUTE KASIR & ADMIN
    Route::get('/pos', [TransactionController::class, 'index'])->name('pos.index');
    Route::post('/pos', [TransactionController::class, 'store'])->name('pos.store');
    
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{id}/print', [HistoryController::class, 'print'])->name('history.print');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 2. RUTE KHUSUS ADMIN / PEMILIK TOKO
    Route::middleware(IsAdmin::class)->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/catalog', [ManagementHubController::class, 'catalog'])->name('catalog.index');
        Route::get('/inventory', [ManagementHubController::class, 'inventory'])->name('inventory.index');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
        Route::get('/debts/create', [DebtController::class, 'create'])->name('debts.create');
        Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
        Route::get('/debts/{debt}', [DebtController::class, 'show'])->name('debts.show');
        Route::post('/debts/{debt}/payments', [DebtController::class, 'storePayment'])->name('debts.payments.store');
        Route::post('/debts/{debt}/cancel', [DebtController::class, 'cancel'])->name('debts.cancel');
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
        Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stock-adjustments.create');
        Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('stock-adjustments.store');
        Route::get('/history/{transaction}/returns', [TransactionReturnController::class, 'index'])->name('history.returns.index');
        Route::post('/history/{transaction}/returns', [TransactionReturnController::class, 'store'])->name('history.returns.store');
        Route::get('/history/returns/{transactionReturn}/print', [TransactionReturnController::class, 'print'])->name('history.returns.print');
        
        // Rute Pengaturan Toko
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        
    }); // Penutup grup IsAdmin
}); // Penutup grup auth

// Rute bawaan sistem Login (Breeze)
require __DIR__.'/auth.php';

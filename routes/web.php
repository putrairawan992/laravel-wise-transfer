<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\DataStockController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\KycController as AdminKycController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::middleware('user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/send-money', [TransferController::class, 'create'])->name('send-money');
        Route::post('/send-money/review', [TransferController::class, 'review'])->name('send-money.review');
        Route::post('/send-money/confirm', [TransferController::class, 'store'])->name('send-money.store');
        
        Route::get('/data-stock', [DataStockController::class, 'index'])->name('data-stock.index');
        
        Route::get('/transactions', [TransferController::class, 'index'])->name('transactions.index');
        Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');
        
        Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');

        Route::get('/kyc', [KycController::class, 'edit'])->name('kyc.edit');
        Route::post('/kyc', [KycController::class, 'update'])->name('kyc.update');
        Route::post('/kyc/submit', [KycController::class, 'submit'])->name('kyc.submit');
        Route::post('/kyc/face-verify', [KycController::class, 'faceVerify'])->name('kyc.faceVerify');
        Route::get('/kyc/download/{type}', [KycController::class, 'download'])->name('kyc.download');
        Route::get('/kyc/view/{type}', [KycController::class, 'view'])->name('kyc.view');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/kyc', [AdminKycController::class, 'index'])->name('kyc.index');
        Route::get('/kyc/{kyc}', [AdminKycController::class, 'show'])->name('kyc.show');
        Route::post('/kyc/{kyc}/approve', [AdminKycController::class, 'approve'])->name('kyc.approve');
        Route::post('/kyc/{kyc}/reject', [AdminKycController::class, 'reject'])->name('kyc.reject');
        Route::get('/kyc/{kyc}/download/{type}', [AdminKycController::class, 'download'])->name('kyc.download');
    });
});

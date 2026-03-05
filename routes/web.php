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

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\CryptoController;

use App\Http\Controllers\Admin\AdminEnquiryController;
use App\Http\Controllers\Admin\IntegrationStatusController;
use App\Http\Controllers\Admin\AdminWhatsAppController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
    // Fiuu Payment Callback (needs to be public usually, or handle csrf)
    Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::post('/payment/notify', [PaymentController::class, 'notify'])->name('payment.notify');
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
        Route::post('/kyc/face-descriptor', [KycController::class, 'registerFaceDescriptor'])->name('kyc.registerFaceDescriptor');
        Route::get('/kyc/download/{type}', [KycController::class, 'download'])->name('kyc.download');
        Route::get('/kyc/view/{type}', [KycController::class, 'view'])->name('kyc.view');
        
        // Face Verification Page
        Route::get('/kyc/face-recognition', [KycController::class, 'faceRecognitionPage'])->name('kyc.face-recognition');

        // Payment Routes
        Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
        Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

        // Enquiry Routes
        Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/create', [EnquiryController::class, 'create'])->name('enquiries.create');
        Route::post('/enquiries', [EnquiryController::class, 'store'])->name('enquiries.store');
        Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
        Route::post('/enquiries/{enquiry}/reply', [EnquiryController::class, 'reply'])->name('enquiries.reply');

        // Crypto Routes
        Route::get('/crypto/wallet', [CryptoController::class, 'index'])->name('crypto.index');
        Route::post('/crypto/wallet', [CryptoController::class, 'checkWallet'])->name('crypto.check');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/kyc', [AdminKycController::class, 'index'])->name('kyc.index');
        Route::get('/kyc/{kyc}', [AdminKycController::class, 'show'])->name('kyc.show');
        Route::post('/kyc/{kyc}/approve', [AdminKycController::class, 'approve'])->name('kyc.approve');
        Route::post('/kyc/{kyc}/reject', [AdminKycController::class, 'reject'])->name('kyc.reject');
        Route::get('/kyc/{kyc}/download/{type}', [AdminKycController::class, 'download'])->name('kyc.download');

        // Admin Enquiry Routes
        Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
        Route::post('/enquiries/{enquiry}/reply', [AdminEnquiryController::class, 'reply'])->name('enquiries.reply');
        Route::patch('/enquiries/{enquiry}/status', [AdminEnquiryController::class, 'updateStatus'])->name('enquiries.updateStatus');

        // Integration Status
        Route::get('/integrations', [IntegrationStatusController::class, 'index'])->name('integrations.index');

        // WhatsApp Manager
        Route::get('/whatsapp', [AdminWhatsAppController::class, 'index'])->name('whatsapp.index');
        Route::post('/whatsapp/send', [AdminWhatsAppController::class, 'send'])->name('whatsapp.send');
    });
});

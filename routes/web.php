<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwoFactorAuthController;


Route::middleware('guest')->group(function () {
    Route::get('/', [TwoFactorAuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [TwoFactorAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [TwoFactorAuthController::class, 'register']);
    Route::post('/login', [TwoFactorAuthController::class, 'login'])->name('sign-in'); // Untuk login
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [TwoFactorAuthController::class, 'logout'])->name('logout');
    Route::get('/verify', [TwoFactorAuthController::class, 'showVerify'])->name('verify');
    Route::get('/verify/resend', [TwoFactorAuthController::class, 'resendOtp'])->name('verify.resend');
    Route::get('/enable-2fa', [TwoFactorAuthController::class, 'enable2fa'])->name('2fa.enable');
    Route::post('/verify-2fa', [TwoFactorAuthController::class, 'verify2fa'])->name('2fa.verify');
    Route::get('/dashboard', [TwoFactorAuthController::class, 'dashboardShow'])->name('dashboard');
});

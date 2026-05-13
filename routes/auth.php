<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'throttle:60,1'])->group(function () {
    
    //peserta_kom
    Route::get('registerPeserta', [RegisteredUserController::class, 'registerPesertaKom_form'])
        ->name('registerPesertaKom_form');
    Route::post('registerPeserta', [RegisteredUserController::class, 'registerPesertaKom']);
   
});


Route::middleware(['guest', 'throttle:60,1'])->group(function () {
    Route::get('/auth/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/auth/register', [RegisteredUserController::class, 'store']);


    Route::get('/auth/register_events', [RegisteredUserController::class, 'create_events'])
        ->name('register');
    Route::post('register_events', [RegisteredUserController::class, 'registerEvent']);


    Route::get('/auth/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/auth/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/auth/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('/auth/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/auth/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/auth/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    //peserta_kom

    //pengajar_kom
    Route::get('registerPengajar', [RegisteredUserController::class, 'registerPengajarKom_form'])
        ->name('registerPengajarKom_form');
    Route::post('registerPengajar', [RegisteredUserController::class, 'registerPengajarKom']);


    //admin_cabang
    Route::get('registerAdminCabang', [RegisteredUserController::class, 'registerAdminCabang_form'])
        ->name('registerAdminCabang_form');
    Route::post('registerAdminCabang', [RegisteredUserController::class, 'registerAdminCabang']);


    //admin_kom
    Route::get('registerAdminKom', [RegisteredUserController::class, 'registerAdminKom_form'])
        ->name('registerAdminKom_form');
    Route::post('registerAdminKom', [RegisteredUserController::class, 'registerAdminKom']);
});


Route::middleware('auth')->group(function () {
    Route::get('/auth/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('/auth/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/auth/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/auth/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('/auth/confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('/auth/password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

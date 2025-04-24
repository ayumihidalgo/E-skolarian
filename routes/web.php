<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;


Route::get('/', function () {
    return view('auth/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/student/dashboard', function () {
        return response()
            ->view('student.dashboard')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('student.dashboard');


    Route::get('/admin/dashboard', function () {
        return response()
            ->view('admin.dashboard')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('admin.dashboard');


    Route::get('/super-admin/dashboard', function () {
        return response()
            ->view('super-admin.dashboard')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('super-admin.dashboard');

});

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetLinkController::class, 'edit'])->name('password.reset');
Route::post('reset-password', [PasswordResetLinkController::class, 'update'])->name('password.update');

Route::get('password-reset-confirmation', function () {
    return view('auth.password-reset-confirmation');
})->name('password.reset.confirmation');


/* Temporary Route for Email Template */
Route::get('/custom-reset-password', function() {
    return view('emails.custom-reset-password');
});

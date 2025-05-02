<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/notification', function () {
    return view('components.general-components.notification');
});

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

    // Using SuperAdminController from DEV branch
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'showDashboard'])->name('super-admin.dashboard');
    
    // Calendar routes from calendar-branch
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // User routes from DEV branch
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications');

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetLinkController::class, 'edit'])->name('password.reset');
Route::post('reset-password', [PasswordResetLinkController::class, 'update'])->name('password.update');

Route::get('password-reset-confirmation', function () {
    return view('auth.password-reset-confirmation');
})->name('password.reset.confirmation');


/* Temporary Route for Email Template */
Route::get('/custom-reset-password', function () {
    return view('emails.custom-reset-password');
});
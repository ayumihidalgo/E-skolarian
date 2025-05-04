<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentController;

// CHANGE THIS //
//Route::get('/', function () {
//    return view('admin.documentArchive');
//});

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
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'showDashboard'])->name('super-admin.dashboard');

    // Calendar routes
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // User routes
    Route::post('/users', [UserController::class, 'store'])->name('users.store');


    Route::get('/super-admin/dashboard', function () {
        return response()
            ->view('super-admin.dashboard')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('super-admin.dashboard');

    Route::get('/admin/review', function () {
        return view('admin.review');
    })->name('admin.review');

    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'showDashboard'])->name('super-admin.dashboard');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');

    // Submit Document Route
    Route::get('/student/submit-documents', function () {
        return view('student.submit-documents');  // resources/views/home.blade.php
    });

    Route::post('/submit-document', [DocumentController::class, 'store'])->name('submit.document');
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

// CHANGE THIS //
// Route::get('/', function () {
//     return view('admin.documentArchive');
// });

// Route for the document preview page (admin)
Route::get('/document/preview/{id}', [AdminDocumentController::class, 'preview'])->name('admin.documentPreview');

// Route for the document preview page (student)
Route::get('/student/document/preview/{id}', [StudentDocumentController::class, 'preview'])
    ->name('student.documentPreview');

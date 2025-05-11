<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\StudentTrackerController;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Middleware\NoBackHistory;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\IndexTwoController;


Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/notification', function () {
    return view('components.general-components.notification');
});



Route::middleware(['auth', NoBackHistory::class])->group(function () {
    Route::get('/student/dashboard', fn () => view('student.dashboard')) -> name('student.dashboard');
    Route::get('/admin/dashboard', fn () => view('admin.dashboard')) -> name('admin.dashboard');

    // Calendar routes
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // User routes
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}', [UserController::class, 'update']);

    // Settings routes
    Route::get('student/settings', [SettingsController::class, 'viewSettings'])->name('student.settings');
    Route::post('student/settings/update-profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('settings.update-profile-picture');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.change-password');

    Route::get('admin/settings', [SettingsController::class, 'viewAdminSettings'])->name('admin.settings');
    Route::post('admin/settings/update-profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('settings.update-profile-picture');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.change-password');

    Route::get('/super-admin/dashboard', fn() => view('super-admin.dashboard'))->name('super-admin.dashboard');
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::get('/check-roles', [UserController::class, 'checkRoles'])->name('check.roles');

    Route::get('/admin/documentReview', [DocumentReviewController::class, 'index'])->name('admin.documentReview');

    Route::get('/super-admin/deactivated-accounts', function () {
        return view('super-admin.deactPage');
    })->name('deactivated.accounts');

    Route::get('/super-admin/deactivated-accounts', [UserController::class, 'deactivatedUsers'])
    ->name('deactivated.accounts');

    Route::get('/admin/review', function () {
        return view('admin.review');
    })->name('admin.review');

    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'showDashboard'])->name('super-admin.dashboard');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');

    // Submit Document Route
    Route::get('/student/submit-documents', [DocumentController::class, 'create'])->name('student.submit-documents');

    Route::post('/submit-document', [DocumentController::class, 'store'])->name('submit.document');

    Route::post('/super-admin/deactivate-user', [SuperAdminController::class, 'deactivateUser'])->name('super-admin.deactivate-user');

    Route::post('/super-admin/reactivate-user/{user}', [UserController::class, 'reactivateUser'])->name('reactivate.user');
    // Dashboard Route
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');

    Route::get('/admin/documentArchive', function () {
        return view('admin.documentArchive');
    })->name('admin.documentArchive');

    Route::get('/student/documentArchive', function () {
        return view('student.documentArchive');
    })->name('student.documentArchive');
    // Route for the document preview page (admin)
Route::get('/document/preview/{id}', [AdminDocumentController::class, 'preview'])->name('admin.documentPreview');
});

// Fetching and displaying and storing comments
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('/comments/{documentId}', [CommentController::class, 'getComments'])->name('comments.get');


Route::middleware(['auth'])->group(function () {
    // Fetching and displaying documents for admin
    Route::get('/admin/documents', [DocumentReviewController::class, 'index'])->name('admin.documents');
    Route::get('/admin/documents/{id}/details', [DocumentReviewController::class, 'getDetails'])->name('admin.documents.details');
    Route::post('/admin/documents/{id}/mark-as-opened', [DocumentReviewController::class, 'markAsOpened'])->name('admin.documents.mark-as-opened');

    // Document approval routes
    Route::post('/admin/documents/{id}/approve', [DocumentReviewController::class, 'approveDocument'])->name('admin.documents.approve');
    Route::post('/admin/documents/{id}/forward', [DocumentReviewController::class, 'forwardDocument'])->name('admin.documents.forward');
    Route::get('/admin/get-admins', [DocumentReviewController::class, 'getAdmins'])->name('admin.get-admins');

    // Document rejection routes
    Route::post('/admin/documents/{id}/reject', [DocumentReviewController::class, 'rejectDocument'])->name('admin.documents.reject');
    Route::post('/admin/documents/{id}/request-resubmission', [DocumentReviewController::class, 'requestResubmission'])->name('admin.documents.request-resubmission');
});

// Route for the student tracker page
Route::get('/student/studentTracker', [StudentTrackerController::class, 'viewStudentTracker'])->name('student.studentTracker');

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


Route::get('/calendar/indexTwo', [IndexTwoController::class, 'viewIndexTwo'])->name('calendar.indexTwo');

// Route for the document preview page (admin)
Route::get('/document/preview/{id}', [AdminDocumentController::class, 'preview'])->name('admin.documentPreview');

// Route for the document preview page (student)
Route::get('/student/document/preview/{id}', [StudentDocumentController::class, 'preview'])
    ->name('student.documentPreview');

// Document viewing
Route::get('/test-pdf', function() {
    return response()->file(public_path('documents/test/sample.pdf'));
});

// Fetching and displaying and storing comments
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('/comments/{documentId}', [CommentController::class, 'getComments'])->name('comments.get');

// Route for the document preview page (student)
Route::get('/student/document/preview/{id}', [StudentDocumentController::class, 'preview'])
    ->name('student.documentPreview');

// Document viewing
Route::get('/documents/{filename}', function ($filename) {
    $path = public_path('documents/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $mimeType = File::mimeType($path);

    // Force inline display for PDFs
    if ($mimeType === 'application/pdf') {
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    // For images and other files
    return response()->file($path);
})->name('document.view')->middleware('auth');

Route::get('/records/{id}', [StudentTrackerController::class, 'show'])->name('records.show');

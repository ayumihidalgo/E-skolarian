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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\IndexTwoController;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsStudent;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StudentDashboardController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

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
});

Route::get('/notification', function () {
    return view('components.general-components.notification');
});



// ----------------------------------------
// Authenticated Routes
// ----------------------------------------
    Route::middleware(['auth', NoBackHistory::class, IsSuperAdmin::class])->group(function () {
        // ---------------- Super Admin ----------------
        Route::get('/super-admin/dashboard', [SuperAdminController::class, 'showDashboard'])->name('super-admin.dashboard');
        Route::get('/super-admin/deactivated-accounts', [UserController::class, 'deactivatedUsers'])->name('deactivated.accounts');
        Route::post('/super-admin/deactivate-user', [SuperAdminController::class, 'deactivateUser'])->name('super-admin.deactivate-user');
        Route::post('/super-admin/reactivate-user/{user}', [UserController::class, 'reactivateUser'])->name('reactivate.user');

    });
    Route::middleware(['auth', NoBackHistory::class, IsAdmin::class])->group(function () {
        // ---------------- Admin ----------------
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'showDashboard'])->name('admin.dashboard');
        Route::post('/admin/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/admin/documentReview', [DocumentReviewController::class, 'index'])->name('admin.documentReview');
        Route::get('/admin/review', fn () => view('admin.review'))->name('admin.review');
        Route::get('/admin/documentHistory', [AdminDocumentController::class, 'documentHistory'])->name('admin.documentHistory');
        Route::get('/document/preview/{id}', [AdminDocumentController::class, 'preview'])->name('admin.documentPreview');
        Route::get('admin/settings', [SettingsController::class, 'viewAdminSettings'])->name('admin.settings');
        Route::post('admin/settings/update-profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('admin.settings.update-profile-picture');
        Route::post('admin/settings/change-password', [SettingsController::class, 'changePassword'])->name('admin.settings.change-password');
        Route::get('/admin/archivePage', action: [AdminDocumentController::class, 'archivePage'])->name('admin.archivePage');


       // Document processing
        Route::get('/admin/documents', [DocumentReviewController::class, 'index'])->name('admin.documents');
        Route::get('/admin/documents/{id}/details', [DocumentReviewController::class, 'getDetails'])->name('admin.documents.details');
        Route::post('/admin/documents/{id}/mark-as-opened', [DocumentReviewController::class, 'markAsOpened'])->name('admin.documents.mark-as-opened');
        Route::post('/admin/documents/{id}/approve', [DocumentReviewController::class, 'approveDocument'])->name('admin.documents.approve');
        Route::post('/admin/documents/{id}/forward', [DocumentReviewController::class, 'forwardDocument'])->name('admin.documents.forward');
        Route::post('/admin/documents/{id}/reject', [DocumentReviewController::class, 'rejectDocument'])->name('admin.documents.reject');
        Route::post('/admin/documents/{id}/request-resubmission', [DocumentReviewController::class, 'requestResubmission'])->name('admin.documents.request-resubmission');
        Route::get('/admin/get-admins', [DocumentReviewController::class, 'getAdmins'])->name('admin.get-admins');
        Route::post('/admin/restore-documents', [AdminDocumentController::class, 'restoreDocuments'])->name('admin.restoreDocuments');
        Route::post('/admin/archive-documents', [AdminDocumentController::class, 'archiveDocuments'])->name('admin.archiveDocuments');
    });
    // ---------------- Student ----------------
    Route::middleware(['auth', NoBackHistory::class, IsStudent::class])->group(function () {
        Route::get('/student/dashboard', [StudentDashboardController::class, 'showStudentDashboard'])->name('student.dashboard');
        Route::get('/student/submit-documents', [DocumentController::class, 'create'])->name('student.submit-documents');
        Route::post('/submit-document', [DocumentController::class, 'store'])->name('submit.document');
        Route::get('/student/documentHistory', [StudentDocumentController::class, 'documentArchive'])->name('student.documentHistory');
        Route::get('/student/studentTracker', [StudentTrackerController::class, 'viewStudentTracker'])->name('student.studentTracker');
        Route::get('/student/document/preview/{id}', [StudentDocumentController::class, 'preview'])->name('student.documentPreview');
        Route::get('student/settings', [SettingsController::class, 'viewSettings'])->name('student.settings');
        Route::post('student/settings/update-profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('student.settings.update-profile-picture');
        Route::post('student/settings/change-password', [SettingsController::class, 'changePassword'])->name('student.settings.change-password');
        Route::post('student/settings/remove-profile', [SettingsController::class, 'removeProfilePicture'])->name('student.settings.remove-profile-picture');
});

Route::middleware(['auth', \App\Http\Middleware\NoBackHistory::class])->group(function () {

    // ---------------- Shared Routes ----------------
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn () => view('student.dashboard'))->name('dashboard');

    // Calendar
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // User
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::get('/check-roles', [UserController::class, 'checkRoles'])->name('check.roles');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check-username');

    Route::get('/admin/documentReview', [DocumentReviewController::class, 'index'])->name('admin.documentReview');

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

    Route::post('/super-admin/reactivate-user', [SuperAdminController::class, 'reactivateUser'])
    ->name('super-admin.reactivate-user')
    ->middleware('auth');
    // Dashboard Route
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');

    Route::get('/admin/documentArchive', function () {
        return view('admin.documentArchive');
    })->name('admin.documentArchive');

    
    // Route for the document preview page (admin)
Route::get('/document/preview/{id}', [AdminDocumentController::class, 'preview'])->name('admin.documentPreview');
});

// ----------------------------------------
// Comments (Shared)
// ----------------------------------------
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('/comments/{documentId}', [CommentController::class, 'getComments'])->name('comments.get');

// ----------------------------------------
// Notifications (Shared)
// ----------------------------------------
Route::get('/notification', fn () => view('components.general-components.notification'));
Route::get('/notifications', fn () => view('notifications'))->name('notifications');
Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->middleware('auth');
Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('student.notifications.index');
});

// ----------------------------------------
// Calendar IndexTwo (Shared)
// ----------------------------------------
Route::get('/calendar/indexTwo', [IndexTwoController::class, 'viewIndexTwo'])->name('calendar.indexTwo');

// ----------------------------------------
        // Document Viewing (Shared)
        // ----------------------------------------
        Route::get('/documents/{filename}', function ($filename) {
            // Set headers for WebAssembly threads support
            header("Cross-Origin-Embedder-Policy: require-corp");
            header("Cross-Origin-Opener-Policy: same-origin");
            
            // Look in the storage/app/public/documents folder
            $path = storage_path('app/public/documents/' . $filename);
            
            if (!file_exists($path)) {
                // Try public path as fallback
                $path = public_path('storage/documents/' . $filename);
                
                if (!file_exists($path)) {
                    return response()->json(['error' => 'File not found'], 404);
                }
            }
            
            // For PDF files, set appropriate headers
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf') {
                // Return the file with headers that encourage browsers to display it
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                    'X-Content-Type-Options' => 'nosniff',
                    'Accept-Ranges' => 'bytes',
                    'Pragma' => 'public',
                    'Cache-Control' => 'public, max-age=86400',
                    'Cross-Origin-Embedder-Policy' => 'require-corp',
                    'Cross-Origin-Opener-Policy' => 'same-origin'
                ]);
            }
            
            // For other files
            return response()->file($path);
        })->name('document.view')->middleware('auth');

        // Debugger
        Route::get('/debug-documents', function () {
            $path1 = storage_path('app/public/documents');
            $path2 = public_path('storage/documents');
            
            $files1 = File::exists($path1) ? File::files($path1) : [];
            $files2 = File::exists($path2) ? File::files($path2) : [];
            
            return [
                'storage_path_exists' => File::exists($path1),
                'public_path_exists' => File::exists($path2),
                'storage_files' => collect($files1)->map(fn($file) => $file->getFilename()),
                'public_files' => collect($files2)->map(fn($file) => $file->getFilename()),
            ];
        })->middleware('auth');

        // WebViewer assets
        Route::get('/webviewer/{path}', function ($path) {
            $fullPath = public_path('webviewer/' . $path);
            
            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'WebViewer asset not found'], 404);
            }
            
            $mime = match(pathinfo($path, PATHINFO_EXTENSION)) {
                'wasm' => 'application/wasm',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'css' => 'text/css',
                'map' => 'application/json',
                default => mime_content_type($fullPath)
            };
            
            return response()->file($fullPath, [
                'Content-Type' => $mime,
                'Cross-Origin-Embedder-Policy' => 'require-corp',
                'Cross-Origin-Opener-Policy' => 'same-origin'
            ]);
        })->where('path', '.*');

// ----------------------------------------
// Records (Shared)
// ----------------------------------------
Route::get('/records/{id}', [StudentTrackerController::class, 'show'])->name('records.show');


Route::get('/loading', function () {
    return view('loading');
});

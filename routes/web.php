<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\LogoutController;

Route::get('/', function () {
    return view('admin.documentArchive');
});

Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// Route for the document preview page (admin)
Route::get('/document/preview/{id}', [AdminDocumentController::class, 'preview'])->name('admin.documentPreview');

// Route for the document preview page (astudent)
Route::get('/student/document/preview/{id}', [StudentDocumentController::class, 'preview'])
    ->name('student.documentPreview');

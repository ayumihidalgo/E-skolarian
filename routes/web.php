<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller; // Import the Controller class

Route::get('/', function () {
    return view('student.documentArchive');
});

Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// Add a route for the document preview page
Route::get('/document/preview/{id}', [Controller::class, 'preview'])->name('document.preview');

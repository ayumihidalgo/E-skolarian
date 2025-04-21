<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logout',[LogoutController::class,'logout'])->name('logout');

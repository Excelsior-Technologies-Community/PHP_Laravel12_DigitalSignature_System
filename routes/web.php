<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthsignController;

// Register
Route::get('register', [AuthsignController::class, 'registerForm'])->name('register.form');
Route::post('register', [AuthsignController::class, 'register'])->name('register.save');

// Login
Route::get('login', [AuthsignController::class, 'loginForm'])->name('login.form');
Route::post('login', [AuthsignController::class, 'login'])->name('login.check');

// Dashboard
Route::get('dashboard', [AuthsignController::class, 'dashboard'])
    ->middleware('authsign')
    ->name('dashboard');

// Signature Page
Route::get('signature', [AuthsignController::class, 'signatureForm'])
    ->middleware('authsign')
    ->name('signature.form');

// Signature Save
Route::post('signature/save', [AuthsignController::class, 'signatureSave'])
    ->middleware('authsign')
    ->name('signature.save');

// Logout
Route::get('logout', [AuthsignController::class, 'logout'])->name('logout');

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

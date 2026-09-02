<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthsignController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\DocumentController;

// Register
Route::get('register', [AuthsignController::class, 'registerForm'])->name('register.form');
Route::post('register', [AuthsignController::class, 'register'])->name('register.save');

// Login
Route::get('login', [AuthsignController::class, 'loginForm'])->name('login.form');
Route::post('login', [AuthsignController::class, 'login'])->name('login.check');

// Forgot Password
Route::get('forgot-password', [AuthsignController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthsignController::class, 'forgotPassword'])->name('password.email');
Route::get('reset-password/{token}', [AuthsignController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('reset-password/{token}', [AuthsignController::class, 'resetPassword'])->name('password.update');

// Email Verification
Route::get('verify-email/{id}', [AuthsignController::class, 'verifyEmail'])->name('verification.verify');

// Dashboard
Route::get('dashboard', [AuthsignController::class, 'dashboard'])
    ->middleware('authsign')
    ->name('dashboard');

// Profile
Route::middleware('authsign')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [ProfileController::class, 'passwordEdit'])->name('profile.password');
    Route::post('profile/password', [ProfileController::class, 'passwordUpdate'])->name('profile.password.update');
});

// Signature Management
Route::middleware('authsign')->group(function () {
    Route::get('signature', [SignatureController::class, 'index'])->name('signature.form');
    Route::post('signature/save', [SignatureController::class, 'store'])->name('signature.save');
    Route::get('my-signatures', [SignatureController::class, 'all'])->name('signatures.all');
    Route::get('my-signatures/{id}/download', [SignatureController::class, 'download'])->name('signatures.download');
    Route::delete('my-signatures/{id}', [SignatureController::class, 'destroy'])->name('signatures.destroy');
});

// Public Document Verification
Route::get('verify-document', [DocumentController::class, 'verificationForm'])->name('documents.verify.form');

Route::post('verify-document', [DocumentController::class, 'verifyByCode'])->name('documents.verify.code');

// Documents
Route::middleware('authsign')->group(function () {
    Route::get('my-documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('my-documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('my-documents/{id}/sign', [DocumentController::class, 'signForm'])->name('documents.sign');
    Route::post('my-documents/{id}/sign', [DocumentController::class, 'sign'])->name('documents.sign.save');
    Route::get('my-documents/{id}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::get('my-documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
});

// Admin
// Admin
Route::middleware(['authsign', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get(
            'dashboard',
            [AdminController::class, 'dashboard']
        )->name('dashboard');


        // Users
        Route::get(
            'users',
            [AdminController::class, 'users']
        )->name('users');

        Route::get(
            'users/search',
            [AdminController::class, 'searchUsers']
        )->name('users.search');

        Route::post(
            'users/{id}/toggle-admin',
            [AdminController::class, 'toggleAdmin']
        )->name('users.toggleAdmin');

        Route::delete(
            'users/{id}',
            [AdminController::class, 'deleteUser']
        )->name('users.delete');


        // Documents
        Route::get(
            'documents',
            [AdminController::class, 'documents']
        )->name('documents');


        // Activity Logs
        Route::get(
            'activity-logs',
            [AdminController::class, 'activityLogs']
        )->name('activity.logs');
    });

// Logout
Route::get('logout', [AuthsignController::class, 'logout'])->name('logout');

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

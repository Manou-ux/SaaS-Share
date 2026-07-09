<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('files', [DashboardController::class, 'uploadFile'])->name('files.store');
    Route::get('files/{fileId}/download', [DashboardController::class, 'downloadFile'])->name('files.download');
    Route::post('messages', [DashboardController::class, 'postMessage'])->name('messages.store');
    Route::get('messages/refresh', [DashboardController::class, 'refreshMessages'])->name('messages.refresh');
    Route::post('workspace/upgrade', [DashboardController::class, 'upgradePlan'])->name('workspace.upgrade');
    Route::post('workspace/downgrade', [DashboardController::class, 'downgradePlan'])->name('workspace.downgrade');
    Route::post('profile/name', [DashboardController::class, 'updateName'])->name('profile.name');
});

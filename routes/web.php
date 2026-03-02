<?php

use App\Http\Controllers\BulkEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Bulk Email Routes
    Route::get('/bulk-email', [BulkEmailController::class, 'index'])->name('bulk-email.index');
    Route::post('/bulk-email/send', [BulkEmailController::class, 'send'])->name('bulk-email.send');
});

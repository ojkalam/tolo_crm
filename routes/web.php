<?php

declare(strict_types=1);

use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

// Public Web & Authentication Routes
Route::get('/', [WebAuthController::class, 'showLogin'])->name('home');
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');

// Interactive Visual Testing Report Dashboard
Route::get('/visual-report', function () {
    return view('visual-report');
})->name('visual.report');

// Role Switcher for instant UI role testing
Route::post('/switch-role', [WebAuthController::class, 'switchRole'])->name('switch.role');

// Protected Web Application Routes
Route::middleware('auth')->group(function () {
    Route::get('/app', function () {
        return view('app');
    })->name('app.dashboard');

    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
});

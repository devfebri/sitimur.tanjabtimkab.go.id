<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::post('/proses_register', [AuthController::class, 'proses_register'])->name('proses_register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');

Route::prefix('admin')->middleware(AdminMiddleware::class)->name('admin_')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('useredit');
    Route::post('/user/create', [UserController::class, 'create'])->name('usercreate');
    Route::delete('/user/{id}', [UserController::class, 'delete'])->name('userdelete');
    // Route::resource('user', UserController::class)->except(['index', 'create', 'edit']);
});


<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PersyaratanController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PpkMiddleware;
use App\Http\Middleware\VerifikatorMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('maintenance');
// });

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::post('/proses_register', [AuthController::class, 'proses_register'])->name('proses_register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('check-username', [UserController::class, 'checkUsername'])->name('usercheckUsername');

Route::prefix('admin')->middleware(AdminMiddleware::class)->name('admin_')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/users/data', [UserController::class,'getUserData'])->name('userdata');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('useredit');
    Route::post('/user/create', [UserController::class, 'create'])->name('usercreate');
    Route::delete('/user/{id}', [UserController::class, 'delete'])->name('userdelete');

    Route::get('/persyaratan', [PersyaratanController::class, 'index'])->name('persyaratan');
    Route::post('/persyaratan/store', [PersyaratanController::class, 'store'])->name('persyaratancreate');
    Route::get('/persyaratan/{id}/edit', [PersyaratanController::class, 'edit'])->name('persyaratanedit');
    Route::delete('/persyaratan/{id}', [PersyaratanController::class, 'destroy'])->name('persyaratandelete');
    Route::get('/persyaratan/{id}/open', [PersyaratanController::class, 'open'])->name('persyaratanopen');
    Route::post('/persyaratan/berkas/store', [PersyaratanController::class, 'berkasStore'])->name('persyaratan_berkas_store');
    Route::get('/persyaratan/berkas/{id}/edit', [PersyaratanController::class, 'berkasEdit'])->name('persyaratan_berkas_edit');
    Route::delete('/persyaratan/berkas/{id}', [PersyaratanController::class, 'berkasDestroy'])->name('persyaratan_berkas_delete');
});
Route::prefix('verifikator')->middleware(VerifikatorMiddleware::class)->name('verifikator_')->group(function () {
    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('pengajuanedit');
    Route::post('/pengajuan/create', [PengajuanController::class, 'kirim_pengajuan'])->name('kirim_pengajuan');
    Route::post('/pengajuan/{id}/update', [PengajuanController::class, 'update_pengajuan'])->name('update_pengajuan');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuandelete');
    Route::get('/pengajuan/{id}/open', [PengajuanController::class, 'open'])->name('pengajuanopen');
    Route::get('/metode_pengadaan_berkas/{id}', [PengajuanController::class, 'metodePengadaanBerkas'])->name('metode_pengadaan_berkas');
    Route::get('pengajuan/{id}/files', [PengajuanController::class, 'getFiles'])->name('pengajuan_files');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
});

Route::prefix('ppk')->middleware(PpkMiddleware::class)->name('ppk_')->group(function () {
    // Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    // Route::post('/kirim_pengajuan', [DashboardController::class, 'kirim_pengajuan'])->name('kirim_pengajuan');

    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('pengajuanedit');
    Route::post('/pengajuan/create', [PengajuanController::class, 'kirim_pengajuan'])->name('kirim_pengajuan');
    Route::post('/pengajuan/{id}/update', [PengajuanController::class, 'update_pengajuan'])->name('update_pengajuan');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuandelete');
    Route::get('/pengajuan/{id}/open', [PengajuanController::class, 'open'])->name('pengajuanopen');
    Route::get('/metode_pengadaan_berkas/{id}',[PengajuanController::class, 'metodePengadaanBerkas'])->name('metode_pengadaan_berkas');
    Route::get('pengajuan/{id}/files', [PengajuanController::class, 'getFiles'])->name('pengajuan_files');
 
    Route::get('pengajuan/{id}/open/edit', [PengajuanController::class, 'editFile'])->name('pengajuan_open_edit');
    Route::post('pengajuan/{id}/open/update', [PengajuanController::class, 'updateFile'])->name('pengajuan_open_update');
});

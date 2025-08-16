<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\DetectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecordController;
// use App\Http\Controllers\Api\RecordApiController;




/*
|--------------------------------------------------------------------------
| Public Routes (bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('login'); // homepage login
Route::post('/login', [AuthController::class, 'login_process'])->name('login.process');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register_create'])->name('register.create');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (harus login dulu, pake middleware)
|--------------------------------------------------------------------------
*/
Route::middleware([AuthMiddleware::class])->group(function () {
    Route::get('/home', [MainController::class, 'index'])->name('home');

    // Route::get('/user', [UserController::class, 'index'])->name('user');
    // Route::post('/user', [UserController::class, 'create'])->name('user.create');
    // Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    // Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::get('/user', [UserController::class, 'index'])->name('user');               // Tampil list user
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create'); // Tampil form tambah user (GET)
    Route::post('/user', [UserController::class, 'store'])->name('user.store');        // Simpan user baru (POST)
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit'); // Tampil form edit user (GET)
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');  // Update user (PUT)
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy'); // Hapus user (DELETE)
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');

    Route::get('/detect', [DetectController::class, 'index'])->name('detect');
    Route::get('/result', [DetectController::class, 'result'])->name('result');
    Route::post('/upload', [DetectController::class, 'upload'])->name('upload');

    Route::get('/record', [RecordController::class, 'index'])->name('record');
    Route::get('/record/submit', [RecordController::class, 'submit'])->name('record.submit');
    Route::get('/record/export', [RecordController::class, 'export'])->name('record.export');
    Route::get('/record/reset', [RecordController::class, 'reset'])->name('record.reset');
});

// Route::get('/api/records', [RecordApiController::class, 'view']);
// Route::post('/api/records/store', [RecordApiController::class, 'store']);
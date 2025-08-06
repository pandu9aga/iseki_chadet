<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\DetectController;

Route::get('/home', [MainController::class, 'index'])->name('home');
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/detect', [DetectController::class, 'index'])->name('detect');
Route::get('/result', [DetectController::class, 'result'])->name('result');
Route::post('/upload', [DetectController::class, 'upload'])->name('upload');
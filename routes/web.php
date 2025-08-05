<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::get('/', [UploadController::class, 'index']);
Route::post('/upload', [UploadController::class, 'upload'])->name('upload');
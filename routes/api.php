<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecordApiController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::get('/records', [RecordApiController::class, 'view']);
Route::post('/records/store', [RecordApiController::class, 'store']);
Route::post('/records/storenew', [RecordApiController::class, 'storenew']);
Route::post('/records/check-prerequisites', [RecordApiController::class, 'checkPrerequisites'])->name('api.chadet.check_prerequisites');
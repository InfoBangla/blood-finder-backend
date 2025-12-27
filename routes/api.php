<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BloodGroupController;
use App\Http\Controllers\DonorController;
use Illuminate\Support\Facades\Route;

$allowed_request_per_minute = config('app.allowed_request_per_minute');

Route::middleware("throttle:{$allowed_request_per_minute},1")->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/donors', [DonorController::class, 'index']);
    Route::post('/donors/register', [DonorController::class, 'register']);
    Route::post('/donors/phone', [DonorController::class, 'phone']);
    Route::get('/donors/search', [DonorController::class, 'search']);
    Route::post('/donors/service-request', [DonorController::class, 'serviceRequest']);
    Route::get('/areas', [AreaController::class, 'index']);
    Route::get('/blood-groups', [BloodGroupController::class, 'index']);
});

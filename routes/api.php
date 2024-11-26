<?php

use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'generateToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('chats', ChatController::class);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

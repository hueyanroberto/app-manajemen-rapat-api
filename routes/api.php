<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::put('/register', [UserController::class, 'updateName']);

    Route::get('/organization', [OrganizationController::class, 'index']);
    Route::post('/organization/create', [OrganizationController::class, 'create']);
    Route::post('/organization/join', [OrganizationController::class, 'join']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
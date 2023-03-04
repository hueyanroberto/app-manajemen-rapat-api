<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/register', [UserController::class, 'updateName']);

    Route::get('/organization', [OrganizationController::class, 'index']);
    Route::post('/organization/create', [OrganizationController::class, 'create']);
    Route::post('/organization/join', [OrganizationController::class, 'join']);

    Route::get('/organization/members/{organization_id}', [OrganizationController::class, 'members']);
    Route::get('/organization/meetings/{organization_id}', [MeetingController::class, 'index'])
            ->middleware('ensure-user-is-in-organization');
    Route::put('/organization/role', [OrganizationController::class, 'changeRole']);
    
    Route::get('/meeting/create/member', [MeetingController::class, 'chooseMember']);
    Route::post('/meeting', [MeetingController::class, 'create']);
    Route::get('/meeting', [MeetingController::class, 'show']);

    Route::post('/agenda', [AgendaController::class, 'store']);

    Route::get('/agenda/suggestion', [SuggestionController::class, 'index']);
    Route::post('/agenda/suggestion', [SuggestionController::class, 'store']);
    Route::put('/agenda/suggestion/accept', [SuggestionController::class, 'changeAcceptanceStatus']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
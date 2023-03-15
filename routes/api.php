<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/register', [UserController::class, 'updateName']);

    Route::get('/organization', [OrganizationController::class, 'index']);
    Route::put('/organization', [OrganizationController::class, 'update']);
    Route::put('/organization/profile', [OrganizationController::class, 'updateProfilePic']);
    Route::post('/organization/create', [OrganizationController::class, 'create']);
    Route::post('/organization/join', [OrganizationController::class, 'join']);

    Route::get('/organization/members/{organization_id}', [OrganizationController::class, 'members']);
    Route::get('/organization/meetings/{organization_id}', [MeetingController::class, 'index'])
            ->middleware('ensure-user-is-in-organization');
    Route::put('/organization/role', [OrganizationController::class, 'changeRole']);
    Route::get('/organization/leaderboard', [OrganizationController::class, 'getLeaderboard']);


    Route::get('/meeting/create/member', [MeetingController::class, 'chooseMember']);
    Route::post('/meeting', [MeetingController::class, 'create']);
    Route::get('/meeting', [MeetingController::class, 'show']);
    Route::put('/meeting', [MeetingController::class, 'update']);
    Route::delete('/meeting', [MeetingController::class, 'delete']);
    Route::post('/meeting/join', [MeetingController::class, 'joinMeeting']);
    Route::post('/meeting/start', [MeetingController::class, 'startMeeting']);
    Route::post('/meeting/end', [MeetingController::class, 'endMeeting']);

    Route::post('/meeting/attachment', [MeetingController::class, 'uploadFile']);
    Route::get('/meeting/minutes', [MeetingController::class, 'getMinutes']);

    Route::post('/agenda', [AgendaController::class, 'store']);
    Route::put('/agenda', [AgendaController::class, 'update']);
    Route::delete('/agenda', [AgendaController::class, 'delete']);

    Route::get('/agenda/suggestion', [SuggestionController::class, 'index']);
    Route::post('/agenda/suggestion', [SuggestionController::class, 'store']);
    Route::put('/agenda/suggestion/accept', [SuggestionController::class, 'changeAcceptanceStatus']);

    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::get('/profile/achievements', [UserController::class, 'getUserAllAchievement']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
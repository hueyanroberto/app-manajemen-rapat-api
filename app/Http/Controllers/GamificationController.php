<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Level;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    public static function updateAchievement($userId, $AchievementIdArray = [])
    {
        foreach ($AchievementIdArray as $i) {
            $userAchievement = UserAchievement::where('user_id', $userId)->where('achievement_id', $i)->first();
            if ($userAchievement) {
                $achievement = Achievement::find($i);
                $progress = $userAchievement->progress;
                if ($progress < $achievement->milestone) {
                    $newProgress = $progress + 1;
                    if ($newProgress >= $achievement->milestone) {
                        $userAchievement->update(['progress' => $newProgress, 'status' => 1]);
                        self::addExp($userId, $achievement->reward_exp);
                        
                        $user = User::find($userId);
                        $data = [
                            'type' => 4,
                            'title' => $achievement->name,
                            'exp' => $achievement->reward_exp
                        ];
                        NotificationController::sendNotification([$user->firebase_token], $data);
                    } else {
                        $userAchievement->update(['progress' => $newProgress]);
                    }
                }
            } else {
                $newUserAchievement = new UserAchievement();
                $newUserAchievement->user_id = $userId;
                $newUserAchievement->achievement_id = $i;
                $newUserAchievement->progress = 1;
                $newUserAchievement->status = 0;
                $newUserAchievement->save();
            }
        }
    }

    public static function addExp($userId, $exp) {
        $currUser = User::where('id', $userId)->first();
        $currUser->loadMissing('level');

        $isLevelUp = false;
        if ($currUser->exp + $exp > $currUser->level->max_exp) {
            $isLevelUp = true;
        }

        $updateValue = ['exp' => $currUser->exp + $exp];
        if ($isLevelUp && $currUser->level->level < 6) {
            $nextLevel = $currUser->level->level + 1;
            $level = Level::where('level', $nextLevel)->first();
            $updateValue[] = ['level_id', $level->id];

            $user = User::find($userId);
            $data = [
                'type' => 5,
                'level' => $level->name
            ];
            NotificationController::sendNotification([$user->firebase_token], $data);
        }
        User::where('id', $userId)->update($updateValue);
    }
}

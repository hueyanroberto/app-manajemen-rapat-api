<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthResource;
use App\Http\Resources\UserResource;
use App\Models\Achievement;
use App\Models\Level;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:45'
        ]); 

        $userCheck = User::where('email', $request->email)->first();

        if ($userCheck) {
            return response()->json(['data' => null]);
        }

        $user = new User();
        $user->email = $request["email"];
        $user->password = Hash::make($request["password"]);
        $user->exp = 0;
        $user->level_id = 1;
        $user->name = "";
        $user->profile_pic = "";

        $user->save();

        $user->loadMissing('level:id,name,level,badge_url');
        $user->token = $user->createToken('user login')->plainTextToken;

        return new UserAuthResource($user);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:45'
        ]); 

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['data' => null]);
        }

        $user->loadMissing('level:id,name,level,badge_url');
        $user["token"] = $user->createToken('user login')->plainTextToken;

        return new UserAuthResource($user);
    }

    public function updateName(Request $request) {

        $request->validate([
            'name' => 'required|min:4|max:45'
        ]);

        $user = Auth::user();
        $userGet = User::where('email', $user->email)->first();

        if ($request["profile_pic"] != "") {
            $image = base64_decode($request["profile_pic"]);
            $filename = "user-" . $user->id . ".jpg";
            file_put_contents('Asset/Profile/User/'.$filename, $image);

            $request['profile_pic'] = $filename;
        } else {
            $request['profile_pic'] = "";
        }

        $userGet->update($request->all());

        return new UserResource($userGet->loadMissing('level:id,name,level,badge_url'));
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success']);
    }

    public function getProfile()
    {
        $authUser = Auth::user();
        $user = User::find($authUser->id);
        $user->loadMissing('level:id,name,level,badge_url,min_exp,max_exp');

        $userAchievement = Achievement::join('user_achievement', 'achievements.id', '=', 'user_achievement.achievement_id')
                ->select('achievements.*')
                ->where('user_achievement.user_id', $user->id)
                ->where('user_achievement.status', 1)
                ->get();
        
        $user->achievement = $userAchievement;

        return new UserResource($user);
    }

    public function getUserAllAchievement()
    {
        $user = Auth::user();
        $achievements = Achievement::select('achievements.*')->get();

        foreach($achievements as $achievement) {
            $userAchievement = UserAchievement::select('progress', 'status')
                ->where('user_id', $user->id)
                ->where('achievement_id', $achievement->id)
                ->first();

            if ($userAchievement) {
                $achievement->progress = $userAchievement->progress;
                $achievement->status = $userAchievement->status;
            } else {
                $achievement->progress = 0;
                $achievement->status = 0;
            }
        }

        return response()->json(['data' => $achievements]);
    }
}

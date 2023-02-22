<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthResource;
use App\Http\Resources\UserResource;
use App\Models\User;
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

        $user->loadMissing('level:id,name,exp,level,badge_url');
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

        $user->loadMissing('level:id,name,exp,level,badge_url');
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

        return new UserResource($userGet->loadMissing('level:id,name,exp,level,badge_url'));
    }
}

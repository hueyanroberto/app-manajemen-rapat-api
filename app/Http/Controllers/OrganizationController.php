<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardResource;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\UserListResource;
use App\Models\LeaderboardHistory;
use App\Models\Level;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:45',
            'description' => 'required',
            'duration' => 'required|integer'
        ]); 

        $code = $this->generateRandomString(7);

        $found = false;
        while (!$found) {
            $searchOrganizaton = Organization::where('code', $code)->first();

            if ($searchOrganizaton) {
                $code = $this->generateRandomString(7);
            } else {
                $found = true;
                $request["code"] = $code;
            }
        }

        date_default_timezone_set("Asia/Jakarta");
        $leaderboardStart = date("Y-m-d");
        $currTime = strtotime($leaderboardStart);
        $leaderboardEnd = date("Y-m-d", strtotime("+". $request['duration'] ." month", $currTime));

        $organization = new Organization();
        $organization->name = $request['name'];
        $organization->description = $request['description'];
        $organization->code = $request['code'];
        $organization->leaderboard_duration = $request['duration'];
        $organization->leaderboard_period = 1;
        $organization->leaderboard_start = $leaderboardStart;
        $organization->leaderboard_end = $leaderboardEnd;
        $organization->save();

        if ($request["profile_pic"] != "") {
            $image = base64_decode($request["profile_pic"]);
            $filename = "organization-" . $organization->id . ".jpg";
            file_put_contents('Asset/Profile/Organization/'.$filename, $image);

            $organization->update(["profile_pic" => $filename]);
        } 

        $user = Auth::user();

        $userOrganization = new UserOrganization();
        $userOrganization["user_id"] = $user->id;
        $userOrganization["organization_id"] = $organization->id;
        $userOrganization["role_id"] = 1;
        $userOrganization->save();

        $userOrganization->loadMissing('role');

        $organization["role"] = $userOrganization["role"];

        return OrganizationResource::collection([$organization]);
    }

    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]); 

        $searchOrganizaton = Organization::where('code', $request["code"])->first();
        if ($searchOrganizaton) {
            $user = Auth::user();
            $findUser = UserOrganization::where('user_id', $user->id)->where('organization_id', $searchOrganizaton->id)->first();
            if (!$findUser) {
                $userOrganization = new UserOrganization();
                $userOrganization["user_id"] = $user->id;
                $userOrganization["organization_id"] = $searchOrganizaton->id;
                $userOrganization["role_id"] = 3;
                $userOrganization->save();

                $userOrganization->loadMissing('role');
                $searchOrganizaton["role"] = $userOrganization["role"];

                return OrganizationResource::collection([$searchOrganizaton]);
            } else {
                return response()->json(['status' => "already joined", 'data' => []]);
            }
        } else {
            return response()->json(['status' => "not found", 'data' => []]);
        }
    }

    public function index()
    {
        $userAuth = Auth::user();
        $user = User::where('id', $userAuth->id)->first();
        $organizations = $user->organizations;
        foreach ($organizations as $organization) {
            $userOrganization = UserOrganization::where('user_id', $user->id)->where('organization_id', $organization->id)->first();
            $userOrganization->loadMissing('role');
            $organization["role"] = $userOrganization["role"];
        }

        return OrganizationResource::collection($organizations);
    }

    public function update(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'name' => 'required|min:2|max:45',
            'description' => 'required',
            'leaderboard_duration' => 'required|integer'
        ]); 

        $organization = Organization::findOrFail($request['organization_id']);
        $organization->update($request->except(['organization_id']));

        return OrganizationResource::collection([$organization]);
    }


    public function updateProfilePic(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'profile_pic' => 'required'
        ]); 

        $organization = Organization::findOrFail($request['organization_id']);

        $image = base64_decode($request["profile_pic"]);
        $filename = "organization-" . $organization->id . ".jpg";
        file_put_contents('Asset/Profile/Organization/'.$filename, $image);
        
        $organization->update(["profile_pic" => $filename]);

        return OrganizationResource::collection([$organization]);
    }

    public function members($organizationId)
    {
        $users = User::join('user_organization', 'users.id', '=', 'user_organization.user_id')
                    ->select('users.*')
                    ->where('user_organization.organization_id', $organizationId)
                    ->orderBy('users.name', 'ASC')->get();

        foreach ($users as $user) {
            $user->loadMissing('level:id,name,level,badge_url');
            $userOrganization = UserOrganization::where('user_id', $user->id)
                    ->where('organization_id', $organizationId)
                    ->first();

            $userOrganization->loadMissing('role');
            $user["role"] = $userOrganization["role"];
        }
        
        return UserListResource::collection($users);
    }

    public function changeRole(Request $request) {
        $request->validate([
            'organization_id' => 'required|integer',
            'user_id' => 'required|integer',
            'role_id' => 'required|integer|min:2|max:3'
        ]); 

        $user = Auth::user();
        $myUserOrganization = UserOrganization::where('organization_id', $request['organization_id'])->where('user_id', $user->id)->first();
        if ($myUserOrganization->role_id != 1) {
            return response()->json(['status' => 'unauthorized', 'data' => null]);
        }

        $user = User::where('id', $request['user_id'])->first();
        $user->loadMissing('level:id,name,level,badge_url');

        $userOrganization = UserOrganization::where('organization_id', $request['organization_id'])
            ->where('user_id', $request['user_id'])
            ->update(['role_id' => $request['role_id']]);
        
        $userOrganization = UserOrganization::where('organization_id', $request['organization_id'])
            ->where('user_id', $request['user_id'])->first();
        $userOrganization->loadMissing('role');
        $user["role"] = $userOrganization["role"];

        return new UserListResource($user);
    }

    public function getLeaderboard(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $request->validate([
            'organization_id' => 'required|integer'
        ]); 

        $organization = Organization::findOrFail($request['organization_id']);

        $leaderboard = User::join('user_organization', 'users.id', '=', 'user_organization.user_id')
                            ->select('users.*', 'user_organization.points_get')
                            ->where('user_organization.organization_id', $organization->id)
                            ->orderBy('user_organization.points_get', 'DESC')->get();

        foreach ($leaderboard as $user) {
            $level = Level::select('id', 'name','level','badge_url')->where('id', $user->level_id)->first();
            $user['level'] = $level;
        }
        
        return response()->json([
            'data' => [
                'start_date' => date("c", strtotime($organization->leaderboard_start)), 
                'end_date' => date("c", strtotime($organization->leaderboard_end)), 
                'duration' => $organization->leaderboard_duration,
                'period' => $organization->leaderboard_period,
                'leaderboard' => LeaderboardResource::collection($leaderboard)
            ]
        ]);
    }

    public function resetLeaderboard()
    {
        date_default_timezone_set("Asia/Jakarta");
        $currDate = date("Y-m-d");
        $organizations = Organization::where('end_date', '<', $currDate)->get();

        foreach ($organizations as $organization) {
            $duration = $organization->leaderboard_duration;
            $period = $organization->leaderbod_period;

            $userOrganizations = UserOrganization::where('organization_id', $organization->id)->get();
            foreach ($userOrganizations as $userOrganization) {
                $leaderboardHistory = new LeaderboardHistory();
                $leaderboardHistory->user_id = $userOrganization->user_id;
                $leaderboardHistory->organization_id = $userOrganization->organization_id;
                $leaderboardHistory->period = $period;
                $leaderboardHistory->point = $userOrganization->points_get;
                $leaderboardHistory->save();

                $userOrganization->update(['points_get' => 0]);

                //TODO sendNotif
            }

            $leaderboardStart = date("Y-m-d");
            $currTime = strtotime($leaderboardStart);
            $leaderboardEnd = date("Y-m-d", strtotime("+". $duration ." month", $currTime));

            $organization->update(['leaderboard_start' => $leaderboardStart, 'leaderboard_end' => $leaderboardEnd, 'leaderboard_period' => $period + 1]);
        }
    }

    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

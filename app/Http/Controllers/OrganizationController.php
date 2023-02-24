<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\UserListResource;
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
            'description' => 'required'
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

        $organization = new Organization($request->only(['name', 'description', 'code']));
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

    public function members($organizationId)
    {
        $users = User::join('user_organization', 'users.id', '=', 'user_organization.user_id')
                    ->select('users.*')
                    ->where('user_organization.organization_id', $organizationId)
                    ->orderBy('users.name', 'ASC')->get();

        foreach ($users as $user) {
            $user->loadMissing('level:id,name,exp,level,badge_url');
            $userOrganization = UserOrganization::where('user_id', $user->id)
                    ->where('organization_id', $organizationId)
                    ->first();

            $userOrganization->loadMissing('role');
            $user["role"] = $userOrganization["role"];
        }
        
        return UserListResource::collection($users);
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

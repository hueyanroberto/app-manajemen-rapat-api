<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeetingDetailResource;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\UserListResource;
use App\Models\Agenda;
use App\Models\Meeting;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserMeeting;
use App\Models\UserOrganization;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'location' => 'required',
            'description' => 'required',
            'organization_id' => 'required|integer'
        ]); 

        date_default_timezone_set("Asia/Jakarta");
        $request['start_time'] = date("Y-m-d H:i:s", strtotime($request['start_time']));
        $request['end_time'] = date("Y-m-d H:i:s", strtotime($request['end_time']));

        $code = $this->generateRandomString(8);

        $found = false;
        while (!$found) {
            $searchMeeting = Meeting::where('code', $code)->first();

            if ($searchMeeting) {
                $code = $this->generateRandomString(8);
            } else {
                $found = true;
                $request["code"] = $code;
            }
        }

        $meeting = new Meeting($request->all());
        $meeting->save();

        if ($request['agendas']) {
            $agendas = $request['agendas'];
            foreach ($agendas as $agenda) {
                $newAgenda = new Agenda();
                $newAgenda->meeting_id = $meeting->id;
                $newAgenda->task = $agenda;
                $newAgenda->completed = 0;
                $newAgenda->save();
            }
        }

        $user = Auth::user();
        $userMeeting = new UserMeeting(['user_id' => $user->id, 'meeting_id' => $meeting->id, 'role' => 1]);
        $userMeeting->save();
        
        if ($request['participants']) {
            $participants = $request['participants'];
            foreach ($participants as $participant_id) {
                $userMeeting = new UserMeeting([
                    'user_id' => $participant_id, 
                    'meeting_id' => $meeting->id
                ]);
                $userMeeting->save();
            }
        }

        return MeetingResource::collection([$meeting]);
    }

    public function index($organization_id)
    {
        date_default_timezone_set("Asia/Jakarta");

        $user = Auth::user();
        $organization = Organization::where('id', $organization_id)->first();
        $meetings = DB::table('meetings')
                    ->join('user_meeting', 'meetings.id', '=', 'user_meeting.meeting_id')
                    ->select('meetings.*')
                    ->where('meetings.organization_id', $organization->id)
                    ->where('user_meeting.user_id', $user->id)
                    ->orderBy('start_time', 'DESC')->get();

        return MeetingResource::collection($meetings);
        
    }

    public function chooseMember(Request $request)
    {
        $request->validate([
            'organizationId' => 'required|integer'
        ]); 

        $organizationId = $request['organizationId'];

        $user = Auth::user();
        $users = User::join('user_organization', 'users.id', '=', 'user_organization.user_id')
                    ->select('users.*')
                    ->where('user_organization.organization_id', $organizationId)
                    ->where('users.id', '!=', $user->id)
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

    public function show(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|integer'
        ]); 

        $user = Auth::user();

        try {
            $meeting = Meeting::findOrFail($request['meeting_id']);

            $userMeeting = UserMeeting::where('meeting_id', $meeting->id)
                ->where('user_id', $user->id)->first();
        
            $agenda = Agenda::select('agendas.id', 'agendas.meeting_id', 'agendas.task', 'agendas.completed')
                ->where('meeting_id', $meeting->id)->get();
            
            $participants = User::join('user_meeting', 'users.id', '=', 'user_meeting.user_id')
                ->select('users.id', 'users.email', 'users.name', 'users.profile_pic', 'user_meeting.status')
                ->where('user_meeting.meeting_id', $meeting->id)
                ->orderBy('users.name', 'ASC')->get();

            foreach($participants as $participant) {
                $userOrganization = UserOrganization::where('user_id', $participant->id)
                    ->where('organization_id', $meeting->organization_id)->first();
                $userOrganization->loadMissing('role:id,name');
                $participant['role'] = $userOrganization['role']->name;
            }

            $meeting['user_status'] = $userMeeting->status;
            $meeting['user_role'] = $userMeeting->role;
            $meeting['agendas'] = $agenda;
            $meeting['participants'] = $participants;

            return new MeetingDetailResource($meeting);
        } catch (Exception $e) {
            return response()->json(["data" => null]);
        }
    }

    public function joinMeeting(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|integer',
            'meeting_code' => 'required',
            'date' => 'required|date'
        ]); 

        $user = Auth::user();
        $meeting = Meeting::findOrFail($request['meeting_id']);
        $code = $request['meeting_code'];

        if ($code != $meeting->code) {
            return response()->json(['status' => 'wrong code', 'data' => null]);
        }

        date_default_timezone_set("Asia/Jakarta");
        $date = strtotime($request['date']);
        $meetDate = strtotime($meeting->start_time);

        if ($date <= $meetDate) {
            //onTime
        } else{
            //late
        }

        UserMeeting::where('meeting_id', $meeting->id)
                ->where('user_id', $user->id)
                ->update(['status' => 1]);

        return $this->show($request);
    }

    public function startMeeting(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|integer',
            'date' => 'required|date'
        ]); 

        $user = Auth::user();
        $meeting = Meeting::findOrFail($request['meeting_id']);

        $userMeeting = UserMeeting::where('meeting_id', $meeting->id)
            ->where('user_id', $user->id)->first();

        if ($userMeeting->role != 1) {
            return response()->json(['status' => 'unauthenticated', 'data' => null]);
        }

        date_default_timezone_set("Asia/Jakarta");
        $date = strtotime($request['date']);
        $meetDate = strtotime($meeting->start_time);

        if ($date <= $meetDate) {
            //onTime
        } else{
            //late
        }

        Meeting::where('id', $meeting->id)
                ->update(['status' => 1]);

        UserMeeting::where('meeting_id', $meeting->id)
                ->where('user_id', $user->id)
                ->update(['status' => 1]);

        return $this->show($request);
    }

    public function endMeeting(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|integer',
            'date' => 'required|date'
        ]); 

        $user = Auth::user();
        $meeting = Meeting::findOrFail($request['meeting_id']);

        $userMeeting = UserMeeting::where('meeting_id', $meeting->id)
            ->where('user_id', $user->id)->first();

        if ($userMeeting->role != 1) {
            return response()->json(['status' => 'unauthenticated', 'data' => null]);
        }

        date_default_timezone_set("Asia/Jakarta");
        $date = strtotime($request['date']);
        $endDate = strtotime($meeting->end_time);

        if ($date <= $endDate) {
            //onTime
        } else{
            //late
        }

        Meeting::where('id', $meeting->id)
                ->update(['status' => 2]);
                
        return $this->show($request);
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

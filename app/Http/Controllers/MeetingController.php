<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeetingResource;
use App\Models\Agenda;
use App\Models\Meeting;
use App\Models\Organization;
use App\Models\UserMeeting;
use App\Models\UserOrganization;
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
        $userMeeting = new UserMeeting(['user_id' => $user->id, 'meeting_id' => $meeting->id]);
        $userMeeting->save();
        
        if ($request['participants']) {
            $participants = $request['participants'];
            foreach ($participants as $participant_id) {
                $userMeeting = new UserMeeting(['user_id' => $participant_id, 'meeting_id' => $meeting->id]);
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

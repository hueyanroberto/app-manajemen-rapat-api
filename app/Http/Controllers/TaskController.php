<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Meeting;
use App\Models\Organization;
use App\Models\Task;
use App\Models\User;
use App\Models\UserMeeting;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|integer',
            'assign_to' => 'required|integer',
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required'
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $request['deadline'] = date("Y-m-d H:i:s", strtotime($request['deadline']));

        $task = new Task();
        $task->meeting_id = $request['meeting_id'];
        $task->assigned_to = $request['assign_to'];
        $task->title = $request['title'];
        $task->description = $request['description'];
        $task->deadline = $request['deadline'];
        $task->save();

        $user = User::find($task->assigned_to);
        $task->user = $user->name;

        return new TaskResource($task);
    }

    public function index($meetingId) {
        $tasks = Task::where('meeting_id', $meetingId)->orderBy('status', 'ASC')->orderBy('deadline', 'ASC')->get();
        
        foreach ($tasks as $task) {
            $user = User::find($task->assigned_to);
            $task->user = $user->name;
        }

        return TaskResource::collection($tasks);
    }

    public function show($taskId)
    {
        $task = Task::findOrFail($taskId);
        $user = User::find($task->assigned_to);
        $task->user = $user->name;
        
        return new TaskResource($task);
    }

    public function updateStatus($taskId, Request $request)
    {
        $request->validate([
            'date' => 'required'
        ]);

        $task = Task::find($taskId);

        $user = Auth::user();
        $UserMeeting = UserMeeting::where('meeting_id', $task->meeting_id)->where('user_id', $user->id)->first();
        
        if ($UserMeeting->role != 1) {
            return response()->json(['status' => 'unauthorized', 'data' => null]);
        }
    
        date_default_timezone_set("Asia/Jakarta");
        $date = strtotime($request['date']);
        $deadline = strtotime($task->deadline);

        $isBeforeDeadline = false;
        if ($date <= $deadline) {
            $organization = Organization::join('meetings', 'organizations.id', '=', 'meetings.organization_id')
                ->where('meetings.id', $UserMeeting->meeting_id)
                ->select('organizations.*')->first();
            $userOrganization = UserOrganization::where('user_id', $task->assigned_to)
                ->where('organization_id', $organization->id)->first();

            $orgPoint = $userOrganization->points_get + 2;
            UserOrganization::where('user_id', $task->assigned_to)
                ->where('organization_id', $organization->id)
                ->update(['points_get' => $orgPoint]);
            
            GamificationController::addExp($task->assigned_to, 2);
            
            $arrAchievementId = [22, 23, 24];
            GamificationController::updateAchievement($task->assigned_to, $arrAchievementId);
            $isBeforeDeadline = true;
        }

        if ($task->status == 1) {
            $task->update(['status'=> 0]);
        } else {
            $task->update(['status'=> 1]);
        }

        $user = User::find($task->assigned_to);
        $task->user = $user->name;

        $data = [
            'type' => 7,
            'title' => $task->title
        ];

        if ($isBeforeDeadline) {
            $data["exp"] = 2;
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        NotificationController::sendNotification([$user->firebase_token], $data);
        
        return new TaskResource($task);
    }
}

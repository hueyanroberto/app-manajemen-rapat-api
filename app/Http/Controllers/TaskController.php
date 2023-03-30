<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function updateStatus($taskId)
    {
        $task = Task::find($taskId);

        $user = Auth::user();
        $userOrganization = UserOrganization::where('meeting_id', $task->meeting_id)->where('user_id', $user->id)->first();
        if ($userOrganization->role != 1) {
            return response()->json(['status' => 'unauthorized', 'data' => null]);
        }

        $task->update(['status'=> 1]);
        
        return new TaskResource($task);
    }
}

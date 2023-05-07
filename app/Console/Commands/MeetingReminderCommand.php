<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationController;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Console\Command;

class MeetingReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MeetingReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set("Asia/Jakarta");
        $meetings = Meeting::whereRaw('DATE(start_time) = DATE()')->get();
        foreach ($meetings as $meeting) {
            $users = User::join('user_meeting', 'users.id', '=', 'user_meeting.user_id')
                ->where('user_meeting.meeting_id', $meeting->id)->get();
            $userToken = array();
            foreach ($users as $user) {
                $userToken[] = $user->firebase_token;
            }
            $data = [
                'type' => 1,
                'start_time' => $meeting->start_time,
                'end_time' => $meeting->end_time,
                'days_left' => 1,
                'title' => $meeting->title
            ];
            NotificationController::sendNotification($userToken, $data);
        }

        $meetings = Meeting::whereRaw('DATE(start_time) - INTERVAL 1 DAY = DATE()')->get();
        foreach ($meetings as $meeting) {
            $users = User::join('user_meeting', 'users.id', '=', 'user_meeting.user_id')
                ->where('user_meeting.meeting_id', $meeting->id)->get();
            $userToken = array();
            foreach ($users as $user) {
                $userToken[] = $user->firebase_token;
            }
            $data = [
                'type' => 1,
                'start_time' => $meeting->start_time,
                'end_time' => $meeting->end_time,
                'days_left' => 1,
                'title' => $meeting->title
            ];
            NotificationController::sendNotification($userToken, $data);
        }
        
        $meetings = Meeting::whereRaw('DATE(start_time) - INTERVAL 2 DAY = DATE()')->get();
        foreach ($meetings as $meeting) {
            $users = User::join('user_meeting', 'users.id', '=', 'user_meeting.user_id')
                ->where('user_meeting.meeting_id', $meeting->id)->get();
            $userToken = array();
            foreach ($users as $user) {
                $userToken[] = $user->firebase_token;
            }
            $data = [
                'type' => 1,
                'start_time' => date("c", strtotime($meeting->start_time)),
                'end_time' => date("c", strtotime($meeting->end_time)),
                'days_left' => 2,
                'title' => $meeting->title
            ];
            NotificationController::sendNotification($userToken, $data);
        }
    }
}

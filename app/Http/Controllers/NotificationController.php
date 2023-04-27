<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public static function sendNotification($userToken, $data)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization' => 'key='. config('app.firebase_notification_key'),
            'Content-Type' => 'application/json'
        ];

        $message = [
            'registration_ids' => $userToken,
            'data' => $data
        ];

        $response = Http::withHeaders($headers)->post($url, $message);
        $statusCode = $response->status();

        $responseBody = json_decode($response->getBody(), true);
        return response()->json($responseBody);
    }

    public function sendNotif()
    {
        $meeting = Meeting::find(1);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization' => 'key=' . config('app.firebase_notification_key'),
            'Content-Type' => 'application/json'
        ];

        $notification = [
            'title' => "Test",
            'body' => "messageBody"
        ];

        $message = [
            'notification' => $notification, 
            'registration_ids' => [
                'f8SM4AnuRjiTBR1SjX3GYN:APA91bGxTxTvnPC-wTsAr1tWq0wz97T9bJ02WXP2kNmGs3uPnZoSa5E9sYNDUDVrMSdU60TK81sYMkvFu5xEEgK17rg5smrfYdl8dGCR0ua77UHYg1DFlJf_vs8J0_HmQVuImlU80upP'
            ],
            'data' => [
                'type' => 1,
                'start_time' => $meeting->start_time,
                'end_time' => $meeting->end_time,
                'days_left' => 2
            ]
        ];

        $response = Http::withHeaders($headers)->post($url, $message);
        $statusCode = $response->status();

        $responseBody = json_decode($response->getBody(), true);
        return response()->json($responseBody);
    }
}

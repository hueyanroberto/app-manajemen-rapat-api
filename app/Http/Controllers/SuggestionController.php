<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuggestionResource;
use App\Models\Achievement;
use App\Models\Agenda;
use App\Models\MeetingPoint;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    public function index(Request $request) {
        $request->validate([
            'agenda_id' => 'required|integer'
        ]);

        $suggestions = Suggestion::where('agenda_id', $request['agenda_id'])->get();

        foreach ($suggestions as $suggestion) {
            $user = User::where('id', $suggestion->user_id)->first();
            $suggestion['user'] = $user->name;
        }

        return SuggestionResource::collection($suggestions);
    }

    public function store(Request $request) {
        $request->validate([
            'agenda_id' => 'required|integer',
            'suggestion' => 'required'
        ]);

        $agenda = Agenda::findOrFail($request['agenda_id']);

        $user = Auth::user();
        $suggestion = new Suggestion();
        $suggestion->user_id = $user->id;
        $suggestion->agenda_id = $request['agenda_id'];
        $suggestion->suggestion = $request['suggestion'];
        $suggestion->save();

        $user = User::where('id', $suggestion->user_id)->first();
        $suggestion['user'] = $user->name;

        $meetingPoint = new MeetingPoint();
        $meetingPoint->user_id = $user->id;
        $meetingPoint->meeting_id = $agenda->meeting_id;
        $meetingPoint->point = 1;
        $meetingPoint->save();

        $arrAchievementId = [4, 5, 6];
        GamificationController::updateAchievement($user->id, $arrAchievementId);

        return new SuggestionResource($suggestion);
    }

    public function changeAcceptanceStatus(Request $request)
    {
        $request->validate([
            'suggestion_id' => 'required|integer'
        ]);

        $suggestion = Suggestion::findOrFail($request['suggestion_id']);
        $acceptance = $suggestion->accepted == 0 ? 1 : 0;
        $suggestion->update(['accepted' => $acceptance]);
        
        $user = User::where('id', $suggestion->user_id)->first();
        $suggestion['user'] = $user->name;

        return new SuggestionResource($suggestion);
    }
}

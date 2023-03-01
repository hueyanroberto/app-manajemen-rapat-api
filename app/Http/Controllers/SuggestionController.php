<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuggestionResource;
use App\Models\Suggestion;
use App\Models\User;
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

        $user = Auth::user();
        $suggestion = new Suggestion();
        $suggestion->user_id = $user->id;
        $suggestion->agenda_id = $request['agenda_id'];
        $suggestion->suggestion = $request['suggestion'];
        $suggestion->save();

        $user = User::where('id', $suggestion->user_id)->first();
        $suggestion['user'] = $user->name;

        return new SuggestionResource($suggestion);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'meeting_id' => 'integer',
            'agendas' => 'array'
        ]);

        $agendas = $request['agendas'];
        
        $newAgendas = array();
        foreach ($agendas as $agenda) {
            $insertAgenda = new Agenda();
            $insertAgenda->task = $agenda;
            $insertAgenda->completed = 0;
            $insertAgenda->meeting_id = $request['meeting_id'];
            $insertAgenda->save();

            $newAgendas[] = $insertAgenda;
        }

        return AgendaResource::collection($newAgendas);
    }

    public function index(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|integer'
        ]); 

        $agenda = Agenda::select('agendas.id', 'agendas.meeting_id', 'agendas.task', 'agendas.completed')
                ->where('meeting_id', $request['meeting_id'])->get();

        return AgendaResource::collection($agenda);
    }

    public function show(Request $request) {
        $request->validate([
            'meeting_id' => 'required|integer'
        ]); 

        $agenda = Agenda::findOrFail($request->agenda_id);
        return AgendaResource::collection([$agenda]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'agenda_id' => 'required|integer',
            'task' => 'required'
        ]);

        $agenda = Agenda::findOrFail($request['agenda_id']);
        $agenda->update($request->only('task'));

        return AgendaResource::collection([$agenda]);
    }

    public function updateStatus(Request $request) {
        $request->validate([
            'agenda_id' => 'required|integer'
        ]);

        $agenda = Agenda::findOrFail($request['agenda_id']);
        $suggestions = Suggestion::where('agenda_id', $agenda->id)->get();
        if ($suggestions->count() > 0) {
            foreach ($suggestions as $suggestion) {
                if ($suggestion->accepted == 1) {
                    $agenda->update(['completed' => 1]);
                    $agenda->completed = 1;
                    break;
                }
            }
        }

        return AgendaResource::collection([$agenda]);
    }

    public function delete (Request $request) 
    {
        $request->validate([
            'agenda_id' => 'required|integer'
        ]);
        
        Suggestion::where('agenda_id', $request['agenda_id'])->delete();

        $agenda = Agenda::findOrFail($request['agenda_id']);
        $agenda->delete();

        return AgendaResource::collection([$agenda]);
    }
}

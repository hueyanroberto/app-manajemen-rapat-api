<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        date_default_timezone_set("Asia/Jakarta");
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_time' => date("c", strtotime($this->start_time)),
            'end_time' => date("c", strtotime($this->end_time)),
            'location' => $this->location,
            'description' => $this->description,
            'code' => $this->code,
            'status' => $this->status,
            'user_status' => $this->user_status,
            'user_role' => $this->user_role,
            'agenda' => $this->agendas,
            'participant' => $this->participants
        ];
    }
}

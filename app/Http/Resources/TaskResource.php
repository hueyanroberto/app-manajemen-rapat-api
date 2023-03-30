<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'meeting_id' => $this->meeting_id,
            'assigned_to' => $this->assigned_to,
            'title' => $this->title,
            'description' => $this->description,
            'deadline' => date("c", strtotime($this->deadline)),
            'status' => $this->status,
            'user' => $this->user
        ];
    }
}

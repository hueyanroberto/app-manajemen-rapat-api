<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'profile_pic' => $this->profile_pic ? $this->profile_pic : "",
            'leaderboard_start' => date("c", strtotime($this->leaderboard_start)),
            'leaderboard_end' => date("c", strtotime($this->leaderboard_end)),
            'leaderboard_period' => $this->leaderboard_period,
            'leaderboard_duration' => $this->leaderboard_duration,
            'role' => $this->role
        ];
    }
}

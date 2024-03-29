<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'exp' => $this->exp,
            'level_id' => $this->level_id,
            'token' => $this->token,
            'profile_pic' => $this->profile_pic,
            'level' => $this->whenLoaded('level'),
            'status' => $this->status
        ];
    }
}

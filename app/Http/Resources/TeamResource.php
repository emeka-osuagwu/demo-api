<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'id' => $this["id"],
            'team_name' => $this["team_name"],
            'team_size' => 6, // Need fix -> get from db
            'created_at' => $this["created_at"],
            'updated_at' => $this["updated_at"],
        ];
    }
}

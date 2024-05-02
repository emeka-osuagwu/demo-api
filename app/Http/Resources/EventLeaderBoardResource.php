<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventLeaderBoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            "id" => $this["team_id"],
            "score" => $this["score"],
            "team_name" => $this["team_name"],
            "highest_score" => $this["highest_score"],
        ];

        /*
        |--------------------------------------------------------------------------
        | return response
        |--------------------------------------------------------------------------
        */
        return $payload;
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SoloLeaderBoardResource extends JsonResource
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
            "id" => $this["id"],
            "score" => $this["score"],
            "full_name" => $this["full_name"],
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

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            "puzzles" => $this["puzzles"] ?? [],
            "player_1" => $this["player_1"],
            "player_2" => $this["player_2"],
            "completed" => $this["completed"],
            "game_time" => $this["game_time"],
            "session_id" => $this["session_id"],
            "created_at" => $this["created_at"],
            "updated_at" => $this["updated_at"],
            "player_1_games" => $this["player_1_games"],
            "player_2_games" => $this["player_2_games"],
            "challenge_accepted" => (bool) $this["challenge_accepted"],
            "player_1_completed" => $this["player_1_completed"],
            "player_2_completed" => $this["player_2_completed"],
        ];
    }
}

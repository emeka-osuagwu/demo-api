<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this['id'],
            'juju' => (int) $this['juju'],
            'begi' => (int) $this['begi'],
            'jara' => (int) $this['jara'],
            'level' => $this['level'] ?? '',
            "email" => $this["email"],
            'score' => (int) $this['score'],
            "totem" => (int) $this["totem"],
            'points' => (int) $this['points'],
            'auth_id' => $this['auth_id'] ?? '',
            'cowries' => (int) $this['cowries'],
            'game_won' => (int) $this['game_won'],
            'player_id' => $this['player_id'],
            "full_name" => $this["full_name"],
            'device_id' => $this['device_id'],
            'giraffing' => (int) $this['giraffing'],
            'updated_at' => $this['updated_at'],
            'game_played' => (int) $this['game_played'],
            'highest_score' => (int) $this['highest_score'],
            'average_score' => (int) $this['average_score'],
            'longest_streak' => (int) $this['longest_streak'],
            'current_streak' => (int) $this['current_streak'],
            "padi_play_wins" => (int) $this["padi_play_wins"],
            "padi_play_losses" => (int) $this["padi_play_losses"],
            "completed_puzzles" => $this["completed_puzzles"],
            "completed_puzzle_levels" => $this["completed_puzzle_levels"]
        ];
    }
}

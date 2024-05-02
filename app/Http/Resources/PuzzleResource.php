<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PuzzleResource extends JsonResource
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
            'word' => $this["word"],
            'status' => $this["status"],
            'level_id' => $this["level_id"],
            'created_at'=> $this["created_at"],
            'updated_at' => $this["updated_at"],
            'description' => $this["description"],
            'level_number' => $this["level_number"],
        ];
    }
}

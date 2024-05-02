<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            "id" => $this["id"] ?? null,
            "title" => $this["title"] ?? null,
            "status" => $this["status"] ?? null,
            "created_at" => $this["created_at"] ?? null,
            "updated_at" => $this["updated_at"] ?? null,
            "description" => $this["description"] ?? null,
        ];

        /*
        |--------------------------------------------------------------------------
        | filter out null values
        |--------------------------------------------------------------------------
        */
        $payload = filterNullValues($payload);

        /*
        |--------------------------------------------------------------------------
        | return response
        |--------------------------------------------------------------------------
        */
        return $payload;
    }
}

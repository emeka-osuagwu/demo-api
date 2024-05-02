<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "name" => $this["name"],
            "amount" => $this["amount"],
            "status" => $this["status"],
            "image_url" => $this["image_url"],
            'created_at' => $this["created_at"],
            'updated_at' => $this["updated_at"],
            "value_type" => $this["value_type"],
            'description' => $this["description"],
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "status" => $this['status'],
            "amount" => $this['amount'],
            'player_id' => $this['player_id'],
            'created_at' => $this['created_at'],
            'updated_at' => $this['updated_at'],
            "payment_method" => $this['payment_method'],
            "payment_channel" => $this['payment_channel'],
            "payment_method_id" => $this['payment_method_id'],
            "transaction_reference" => $this['transaction_reference'],
        ];
    }
}

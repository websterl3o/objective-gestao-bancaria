<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountCreateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'numero_conta' => $this->uuid,
            'saldo' => $this->balance,
            'data_criacao' => $this->created_at,
            'data_atualizacao' => $this->updated_at,
        ];
    }
}

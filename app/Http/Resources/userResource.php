<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class userResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => (string) $this->id,
            'FirstName' => $this->FirstName,
            'LastName' => $this->LastName,
            'Email' => $this->Email,    
            'Number' => $this->Number,
            'Age' => $this->Age,
            'Wallet' => $this->Wallet,
        ];
    }
}

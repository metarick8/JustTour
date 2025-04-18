<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class teamWithTripsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team_id' => $this->id,
            'TeamName' => $this->TeamName,
            'Email' => $this->Email,
            'Description' => $this->Description,
            'ContactInfo' => $this->ContactInfo,
            'Rate' => $this->Rate,
            'Wallet' => $this->Wallet,
            'ProfilePhoto' => $this->ProfilePhoto,
            'Followers' => $this->UserTeam->count() ?: 0,
            'trips' => tripResource::collection($this->Trips),
        ];
    }
}

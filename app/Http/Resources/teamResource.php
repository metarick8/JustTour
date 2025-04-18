<?php

namespace App\Http\Resources;

use App\Models\UserTeam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class teamResource extends JsonResource
{
    public function toArray(Request $request, $withTrips = "yes"): array
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
        ];
    }
}

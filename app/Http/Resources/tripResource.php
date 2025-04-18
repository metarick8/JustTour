<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class tripResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $team = Team::find($this->TeamId);
        $totalContestants = $this->reserveTrips()->sum('Count');
        return [
            'id' => $this->id,
            'team_id' => (string) $this->TeamId,
            'TeamName'=> $team ? $team->TeamName : null,
            'Title' => $this->Title,
            'Title' => $this->Title,
            'StartDate' => $this->StartDate,
            'EndDate' => $this->EndDate,
            'StartBooking' => $this->StartBooking,
            'EndBooking' => $this->EndBooking,
            'Location' => $this->Location,
            'Type' => $this->Type,
            'Level' => $this->Level,
            'SubLimit' => $this->SubLimit,
            'Cost' => $this->Cost,
            'Description' => $this->Description,
            'Retrieve' => $this->Retrieve,
            'Requirements' => $this->Requirements,
            'Rate' => $this->Rate,
            'TripPhoto' => $this->TripPhoto,
            "Status" => $this->Status,
            'RetrieveEndDate' => optional($this->retrieve)->EndDate,
            'Percent' => optional($this->retrieve)->Percent,
            'contestants' => $totalContestants,
        ];
    }
}

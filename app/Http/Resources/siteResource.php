<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class siteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "SiteName" => $this->SiteName,
            "Loction" => $this->Location,
            "Details" => $this->Details,
            "Rate" => $this->Rate,
        ];
    }
}

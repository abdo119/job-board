<?php
// app/Http/Resources/LocationResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            // Add other fields as needed
        ];
    }
}

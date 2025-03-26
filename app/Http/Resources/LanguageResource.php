<?php

// app/Http/Resources/LanguageResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // Add other fields as needed
        ];
    }
}

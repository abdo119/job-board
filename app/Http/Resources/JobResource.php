<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'company_name' => $this->company_name,
            'salary_range' => $this->when(
                !is_null($this->salary_min) || !is_null($this->salary_max),
                [
                    'min' => $this->salary_min,
                    'max' => $this->salary_max
                ]
            ),
            'is_remote' => $this->is_remote,
            'job_type' => $this->job_type,
            'status' => $this->status,
            'dates' => [
                'published' => $this->published_at?->toISOString(),
                'created' => $this->created_at?->toISOString(),
                'updated' => $this->updated_at?->toISOString()
            ],
            'languages' => $this->whenLoaded('languages', fn () => $this->languages->pluck('name')),
            'locations' => $this->whenLoaded('locations', fn () => $this->locations->map(fn ($location) => [
                'city' => $location->city,
                'state' => $location->state,
                'country' => $location->country
            ])),
            'categories' => $this->whenLoaded('categories', fn () => $this->categories->pluck('name')),
            'attributes' => $this->whenLoaded('attributeValues', function () {
                $this->attributeValues->loadMissing('attribute');
                return $this->attributeValues->pluck('value', 'attribute.name');
            }),
        ];
    }
}

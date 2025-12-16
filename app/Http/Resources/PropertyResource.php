<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'agentId' => $this->agent_id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'propertyType' => $this->property_type,
            'listingType' => $this->listing_type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'size' => $this->size,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'status' => $this->status,
            
        ];
    }
}

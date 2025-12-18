<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'userId' => $this->user_id,
            'propertyId' => $this->property_id,
            'name' => $this->name,
            'email' => $this->email,
            'phoneNumber' => $this->phone_number,
            'appointmentDate' => $this->appointment_date,
            'description' => $this->description
        ];
    }
}

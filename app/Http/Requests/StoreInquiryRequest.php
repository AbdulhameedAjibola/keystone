<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'property_id' => 'required|integer|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'nullable|string|max:15',
            'appointment_date' => 'nullable|date',
            'description' => 'required|string'
        ];
    }

    protected function prepareForValidation(){
        $this->merge([
            'user_id' => $this->userId,
            'property_id' => $this->propertyId,
            'phone_number' => $this->phoneNumber,
            'appointment_date' => $this->appointmentDate,
        ]);
    }
}

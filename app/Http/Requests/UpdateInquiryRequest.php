<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInquiryRequest extends FormRequest
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
        
        $method = $this->method();
        if($method === 'PUT') {
            return [
                 'user_id' => 'nullable|integer|exists:users,id',
                'property_id' => 'required|integer|exists:properties,id',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone_number' => 'nullable|string|max:15',
                'appointment_date' => 'nullable|date',
                'description' => 'required|string'
            ];
        } else{
            return [
                'user_id' => 'sometimes|nullable|integer|exists:users,id',
                'property_id' => 'sometimes|required|integer|exists:properties,id',
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255',
                'phone_number' => 'sometimes|nullable|string|max:15',
                'appointment_date' => 'sometimes|nullable|date',
                'description' => 'sometimes|required|string'
            ];
        }
       
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

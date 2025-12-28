<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       
        return [
           
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'property_type' => 'required|string|in:house,apartment,shortlet,penthouse,land,commercial',
            'listing_type' => 'required|string|in:sale,rent',
            'status' => 'nullable|string|in:available,sold,unavailable',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'size' => 'nullable|numeric',
            'address' => 'required|string|max:500',
            'area' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
        ];
    }

    protected function prepareForValidation(){
        $this->merge([
            
            'property_type' => $this->propertyType,
            'listing_type' => $this->listingType,

        ]);
    }
}

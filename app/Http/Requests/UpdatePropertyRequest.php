<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
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

        if($method === 'PUT'){
            return[
                 
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
        }else{
            return[
                
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'price' => 'sometimes|required|numeric',
                'property_type' => 'sometimes|required|string|in:house,apartment,shortlet,penthouse,land,commercial',
                'listing_type' => 'sometimes|required|string|in:sale,rent',
                'status' => 'nullable|string|in:available,sold,unavailable',
                'bedrooms' => 'sometimes|nullable|integer',
                'bathrooms' => 'sometimes|nullable|integer',
                'size' => 'sometimes|nullable|numeric',
                'address' => 'sometimes|required|string|max:500',
                'city' => 'sometimes|required|string|max:100',
                'state' => 'sometimes|required|string|max:100',
            ];
        }
    }

     protected function prepareForValidation(){
        $this->merge([
            
            'property_type' => $this->propertyType,
            'listing_type' => $this->listingType,

        ]);
    }
}

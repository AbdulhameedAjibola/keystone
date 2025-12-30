<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCareerRequest extends FormRequest
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
           
            'title' => 'required|string|max:255',
        
            'type' => 'required|string|in:full-time,part-time,contract, internship',
            'description' => 'required|string',
            
           
            'application_deadline' => 'nullable|date',

        ];
    }

    protected function prepareForValidation(){
        $this->merge([

            'application_deadline' => $this->applicationDeadline,
        ]);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCareerRequest extends FormRequest
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
                
                'title' => 'required|string|max:255',
                
                'type' => 'required|string|in:full-time,part-time,contract, internship',
                'description' => 'required|string',
                
                'application_deadline' => 'nullable|date',
               
            ];
        } else{
            return [
                
                'title' => 'sometimes|required|string|max:255',
                'location' => 'sometimes|required|string|max:255',
                'type' => 'sometimes|required|string|in:full-time,part-time,contract, internship',
                'description' => 'sometimes|required|string',
                'requirements' => 'sometimes|required|string',
                'salary' => 'sometimes|nullable|string|max:100',
                
                'application_deadline' => 'sometimes|nullable|date',
            ];
        }
        
    }

     protected function prepareForValidation(){
        $this->merge([
           
           
            'application_deadline' => $this->applicationDeadline,
        ]);
    }
}

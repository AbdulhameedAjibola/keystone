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
                'user_id' => 'required|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'type' => 'required|string|in:full-time,part-time,contract, internship',
                'description' => 'required|string',
                'requirements' => 'required|string',
                'salary' => 'nullable|string|max:100',
                'is_active' => 'required|boolean|default:true',
                'application_deadline' => 'nullable|date',
            ];
        } else{
            return [
                'user_id' => 'sometimes|required|integer|exists:users,id',
                'title' => 'sometimes|required|string|max:255',
                'location' => 'sometimes|required|string|max:255',
                'type' => 'sometimes|required|string|in:full-time,part-time,contract, internship',
                'description' => 'sometimes|required|string',
                'requirements' => 'sometimes|required|string',
                'salary' => 'sometimes|nullable|string|max:100',
                'is_active' => 'sometimes|required|boolean|default:true',
                'application_deadline' => 'sometimes|nullable|date',
            ];
        }
        
    }

     protected function prepareForValidation(){
        $this->merge([
            'user_id' => $this->userId,
            "is_active" => $this->isActive,
            'application_deadline' => $this->applicationDeadline,
        ]);
    }
}

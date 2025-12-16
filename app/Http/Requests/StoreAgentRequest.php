<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgentRequest extends FormRequest
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
          'name' => 'required|string|max:255',
          'email' => 'required|string|email|max:255|unique:agents',
          'password' => 'required|string|min:10',
          'role' => 'required|string|default:agent',
          'verification_code' => 'nullable|string|max:16',
          'verification_document_url' => 'nullable|string|max:255',
          'status' => 'required|string|default:pending',
          'phone_number' => 'nullable|string|max:15',
          'address' => 'nullable|string',
          'city' => 'nullable|string|max:11',
          'state' => 'nullable|string|max:11',
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge([
            'verification_code' => $this->verification_code ?? null,
            'verification_document_url' => $this->verification_document_url ?? null,
            'phone_number' => $this->phone_number ?? null,
        ]);
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust according to your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'domain' => 'sometimes|string|max:255|unique:landlord.tenants,domain,' . $this->route('tenant'),
            'database' => 'sometimes|nullable|string|max:64|unique:landlord.tenants,database,' . $this->route('tenant'),
            'plan_id' => 'sometimes|nullable|exists:landlord.plans,id',
            'trial_ends_at' => 'sometimes|nullable|date',
            'subscription_ends_at' => 'sometimes|nullable|date',
            'is_active' => 'sometimes|boolean',
            'settings' => 'sometimes|nullable|array'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Custom error messages
        ];
    }
}

<?php

namespace App\Http\Requests\Role;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' =>ucwords($this->input('name')),

        ]);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roleId = $this->route('role');
        return [
            'name' => [
                'nullable',
                'string',
                $roleId ? Rule::unique('roles')->ignore($roleId) : 'unique:roles,name',
            ],
            'permission' => 'nullable|array',
            'permission.*' => 'integer',
        ];
    }
}

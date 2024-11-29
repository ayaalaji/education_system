<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
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
            'name' => 'nullable|string|min:3|max:100',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8',
            'specialization' =>'nullable|string|max:255',
            'role' =>'nullable|in:manager,admin,teacher'
        ];
    }
}

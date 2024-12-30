<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
        ];
    }
     /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The category name must be unique.',
            'teacher_id.required' => 'The teacher ID is required.',
            'teacher_id.exists' => 'The selected teacher does not exist.',
        ];
}
}
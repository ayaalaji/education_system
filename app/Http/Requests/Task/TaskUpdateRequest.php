<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
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
           'title' => 'nullable|string|unique:tasks,title|min:8',
           'due_date' => 'nullable|date|after:now',
           'status' => 'nullable|in:Complete,UnComplete',
           'course_id' => 'nullable|integer|exists:courses,id'
        ];
    }
}

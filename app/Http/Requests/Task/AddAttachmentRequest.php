<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class AddAttachmentRequest extends FormRequest
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
            'file_path' => 'required|file|mimes:pdf,txt,doc,docx,xlsx,xls,jpeg,png,gif,webp|max:10240',
            'summation_date' => 'required|date|after_or_equal:today',
        ];
    }
}

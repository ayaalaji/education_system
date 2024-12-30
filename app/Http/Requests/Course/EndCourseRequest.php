<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class EndCourseRequest extends FormRequest
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
               'end_date'     => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
        ];
    }

    
    public function attributes()
    {
        return [
            'end_date' => 'course end date',
        ];
    }

    public function messages()
    {
        return [ 
            'required' => 'The :attribute is required.',
            'date_format' => 'The :attribute must be in format Year - month - day.',
            'date' => 'The :Attribute must be date type',    
            'end_date.after_or_equal' => 'The :attribute must be after or equal to the course start date.',

        ];
    }

}

<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EndRegisterCourseRequest extends FormRequest
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
              'end_register_date'   => 'required|date|date_format:Y-m-d|after_or_equal:start_register_date',
        ];
    }

        //............................................

        public function attributes()
        {
            return [
                'end_register_date' => 'registration end date',

            ];
        }
        
        
        //..............................................
        public function messages()
        {
            return [
                'required' => 'The :attribute is required.',
                'date_format' => 'The :attribute must be in format Year - month - day.',
                'date' => 'The :Attribute must be date type',  
                'end_register_date.after_or_equal' => 'The :attribute must be after or equal to the start registration date.',

        
            ];
        }
        
        
    
}

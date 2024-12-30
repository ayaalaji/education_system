<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StartRegisterCourseRequest extends FormRequest
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
             'start_register_date' => 'required|date|date_format:Y-m-d|before_or_equal:end_register_date',
        ];
    }

        //............................................

        public function attributes()
        {
            return [
                'start_register_date' => 'registration start date',

            ];
        }
        
        
        //..............................................
        public function messages()
        {
            return [
                'required' => 'The :attribute is required.',
                'date_format' => 'The :attribute must be in format Year - month - day.',
                'date' => 'The :Attribute must be date type',  
                'start_register_date.before_or_equal' => 'The :attribute must be before or equal to the end registration date.',
        
            ];
        }
        
        
    
}

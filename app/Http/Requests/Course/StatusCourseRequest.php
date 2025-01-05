<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StatusCourseRequest extends FormRequest
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
            'status'   => 'required|in:Open,Closed',  
        ];
    }

      //............................................

      public function attributes()
      {
          return [
              'status' => 'course status',
          ];
      }
      
      
      //..............................................
      public function messages()
      {
          return [
            'required' => 'The :attribute is required.',
            'in' => 'The :attribute must be Open or Closed',
    
          ];
      }
      
      
      
      
}

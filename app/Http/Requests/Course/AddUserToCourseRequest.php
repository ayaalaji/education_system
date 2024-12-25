<?php

namespace App\Http\Requests\Course;

use App\Models\User;
use App\Services\CourseService;
use Illuminate\Foundation\Http\FormRequest;

class AddUserToCourseRequest extends FormRequest
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
            'users'       => 'required|array',
            'users.*'     => 'required|exists:users,id',
            
        ];

    }

    //.....................................
    //.....................................


    public function attributes()
    {
       return[
        'students' => 'students list',
        'students.*' => 'student ID',
       ]; 
    }

    //...................................
    //...................................

    public function message()
    {
        return[
            'required' => 'Each student must have a valid :attribute.',
            'array' => 'The student must be :attribute.',
            'exists' => 'The selected :attribute is not exists.',
        ];
    }
  
}

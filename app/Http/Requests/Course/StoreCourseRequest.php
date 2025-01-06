<?php

namespace App\Http\Requests\Course;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCourseRequest extends FormRequest
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
            'title'                  => 'required|string|max:255',
            'description'            => 'required|string',
            'course_duration'        => 'required|integer',
            'category_name'          => 'required|exists:categories,name',
        ];
    }

    //.......................................
 
    public function passedValidation()
        {
            $category_id = Category::where('name', $this->category_name)->pluck('id')->first();
     
            $this->merge([
                'teacher_id'  => auth('teacher-api')->id(),
                'category_id' => $category_id,
            ]);
        }
        
    

    //............................................

    public function attributes()
    {
        return [
            'title' => 'course title',
            'course_duration' => 'course duration',
            'description' => 'course description',
            'category_name' => 'category name',

        ];
    }



//..............................................
public function messages()
{
    return [
        'required' => 'The :attribute is required.',
        'string' => 'the :attribut must be string',
        'title.max'   => 'The course must me at max 100 carecter',
        'category_id.exists' => 'The selected :attribute is invalid.',
        'course_duration.max' => 'The :attribute must be 10 carectar at max .',

    ];
}


}

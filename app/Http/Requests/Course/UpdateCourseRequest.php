<?php

namespace App\Http\Requests\Course;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCourseRequest extends FormRequest
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
            'title'               => 'nullable|string|max:255',
            'description'         => 'nullable|string',
            'course_duration'     => 'nullable|integer', 
            'category_name'       => 'nullable|exists:categories,name',
        ];
    }

    //.......................................

    public function passedValidation()
    {
        $category_id = Category::where('name', $this->category_name)->pluck('id')->first();

        $this->merge([
          'category_id'   =>  $category_id
        ]);
    }

    public function attributes()
{
    return [
        'title' => 'course title',
        'description' => 'course description',
        'course_duration' => 'course duration',
        'category_name' => 'category name',
    ];
}


//..............................................
public function messages()
{
    return [
        'category_name.exists' => 'The selected :attribute is invalid.',
        'string' => 'the :attribut must be string',
        'course_duration.max' => 'The :attribute must be 10 carectar at max .',
    ];
}



 
}



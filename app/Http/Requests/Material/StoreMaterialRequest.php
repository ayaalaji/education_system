<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
            'title' => 'required|string|max:255', // العنوان مطلوب ونصي بطول أقصى 255 حرفًا
            'file_path' => 'required|file|mimes:pdf|max:50240',
            'video_path' => 'nullable|file|mimes:mp4,MP4|max:50240',
            'course_id' => 'required|exists:courses,id', // معرف الدورة مطلوب ويجب أن يكون موجودًا في جدول courses
        ];
    }
     /**
     * رسائل الأخطاء .
     */
    public function messages(): array
    {
        return [
            'title.required' => 'حقل العنوان مطلوب.',
            'file_path.required' => 'حقل مسار الملف مطلوب.',
            'course_id.required' => 'حقل معرف الدورة مطلوب.',
            'course_id.exists' => 'الدورة المحددة غير موجودة.',
            'video_path.mimes' => 'يجب أن يكون الفيديو من نوع MP4.',
            'video_path.max' => 'يجب ألا يتجاوز حجم الفيديو 20 ميجابايت.',

        ];
    }
}

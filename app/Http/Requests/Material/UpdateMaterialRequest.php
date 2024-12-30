<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255', // إذا تم إرسال العنوان، يجب أن يكون مطلوبًا ونصيًا بحد أقصى 255 حرفًا
            'file_path' => 'sometimes|required|string|max:255', // إذا تم إرسال مسار الملف، يجب أن يكون مطلوبًا ونصيًا
            'video_path' => 'nullable|string|max:255', // مسار الفيديو اختياري ونصي
            'course_id' => 'sometimes|required|exists:courses,id', // إذا تم إرسال معرف الدورة، يجب أن يكون موجودًا
        ];
    }

    /**
     * رسائل الأخطاء 
     */
    public function messages(): array
    {
        return [
            'title.required' => 'حقل العنوان مطلوب إذا تم إرساله.',
            'file_path.required' => 'حقل مسار الملف مطلوب إذا تم إرساله.',
            'course_id.exists' => 'الدورة المحددة غير موجودة.',
        ];
    }
}
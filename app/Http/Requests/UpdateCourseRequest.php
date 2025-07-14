<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- PENTING: TAMBAHKAN INI

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ubah menjadi true agar request diizinkan
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
            // Ini adalah aturan validasi yang kita pindahkan
            'title' => ['required','string','max:255', Rule::unique('courses')->ignore($this->course->id)],
            'course_code' => ['nullable','string','max:50', Rule::unique('courses')->ignore($this->course->id)],
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'final_exam_date' => 'nullable|date',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'prerequisites' => 'nullable|array', 
            'prerequisites.*' => 'integer|exists:courses,id',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
        ];
    }
}
<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // <-- Diubah menjadi true
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50|unique:courses,course_code',
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'final_exam_date' => 'nullable|date',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'integer|exists:courses,id'
        ];
    }
}
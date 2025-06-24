<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi sudah ditangani oleh middleware pada route
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                // Judul kuis harus unik untuk setiap kursus
                Rule::unique('quizzes')->where('course_id', $this->course->id),
            ],
            'description'   => 'nullable|string',
            'duration'      => 'required|integer|min:1',
            'passing_grade' => 'required|integer|min:0|max:100',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Impor class Rule

class UpdateLearningPathRequest extends FormRequest
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
        // 'learning_path' didapat dari nama parameter di route: {learning_path}
        $learningPathId = $this->route('learning_path')->id;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('learning_paths')->ignore($learningPathId), // Aturan unik dengan pengecualian
            ],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ];
    }
}
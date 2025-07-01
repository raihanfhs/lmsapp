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
        'title'         => 'required|string|max:255',
        'description'   => 'nullable|string',
        'duration'      => 'required|integer|min:1',
        'pass_grade'    => 'required|integer|min:0|max:100',
        // This assumes you added 'max_attempts' to the fillable array in the Quiz model
        'max_attempts'  => 'nullable|integer|min:1', 
        ];
    }
}
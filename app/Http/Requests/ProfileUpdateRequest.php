<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Import Rule

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Get the current user from the request
        $user = $this->user();

        return [
            // Existing rules for name and email
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)], // Ensure email is unique, ignoring the current user

            // --- New Rules ---

            // Common Profile Fields
            'bio' => ['nullable', 'string', 'max:1000'], // Max length for bio text
            'phone_number' => ['nullable', 'string', 'max:25'], // Max length for phone

            // Address Fields
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Avatar Upload
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'], // Nullable (optional), must be image, specific types, max 2MB

            // --- Role-Specific Fields ---
            // We use Rule::requiredIf to make fields required only if the user has the specific role

            // Student Fields
            'student_id_number' => [
                Rule::requiredIf(fn() => $user->hasRole('Student')), // Required only if user is Student
                'nullable', // Allows it to be nullable if not required
                'string',
                'max:50',
                 // Make it unique in student_details table, ignoring the current user's potential existing record
                 Rule::unique('student_details', 'student_id_number')->ignore($user->studentDetail?->id),
                 // Note: Using studentDetail?->id requires the relationship to be loaded or needs careful handling if detail doesn't exist yet.
                 // Simpler alternative if ID collision is less likely during update:
                 // Rule::unique('student_details')->ignore($user->id, 'user_id'),
            ],
            'enrollment_date' => [
                Rule::requiredIf(fn() => $user->hasRole('Student')),
                'nullable',
                'date' // Must be a valid date format
            ],
            'major' => [
                 Rule::requiredIf(fn() => $user->hasRole('Student')),
                 'nullable',
                 'string',
                 'max:100'
            ],

            // Teacher Fields
             'employee_id_number' => [
                Rule::requiredIf(fn() => $user->hasRole('Teacher')),
                'nullable',
                'string',
                'max:50',
                Rule::unique('teacher_details', 'employee_id_number')->ignore($user->teacherDetail?->id),
                 // Simpler alternative: Rule::unique('teacher_details')->ignore($user->id, 'user_id'),
            ],
             'qualification' => [
                 Rule::requiredIf(fn() => $user->hasRole('Teacher')),
                 'nullable',
                 'string',
                 'max:255'
             ],
             'department' => [
                 Rule::requiredIf(fn() => $user->hasRole('Teacher')),
                 'nullable',
                 'string',
                 'max:100'
             ],
            'skills' => [
                Rule::requiredIf(fn() => $this->user()->hasRole('Teacher')), // Only required if user is Teacher
                'nullable', // Allow empty submission if not required or if teacher unselects all
                'array'
            ],
            'skills.*' => [ // Validate each item in the skills array
                    'integer',
                    'exists:skills,id'
            ],
        ];
    }
}
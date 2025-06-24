<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('pengelola.courses.store') }}" class="space-y-6">
                        @csrf

                        {{-- Title --}}
                        <div>
                            <x-input-label for="title" :value="__('Course Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full dark:text-gray-900" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- Course Code --}}
                        <div>
                            <x-input-label for="course_code" :value="__('Course Code (Optional)')" />
                            <x-text-input id="course_code" name="course_code" type="text" class="mt-1 block w-full" :value="old('course_code')" />
                            <x-input-error class="mt-2" :messages="$errors->get('course_code')" />
                        </div>

                        {{-- Description --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        {{-- Prerequisite Selection --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-input-label :value="__('Course Prerequisites (Optional)')" class="mb-2 font-semibold" />
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Select any courses that must be completed before a student can enroll in this one.</p>
                            <div class="mt-2 space-y-2 border p-4 rounded-md h-48 overflow-y-auto">
                                @forelse ($allCourses as $prereqCourse)
                                    <label for="prereq_{{ $prereqCourse->id }}" class="flex items-center">
                                        <input id="prereq_{{ $prereqCourse->id }}"
                                            name="prerequisites[]" {{-- Name as array for multiple selections --}}
                                            type="checkbox"
                                            value="{{ $prereqCourse->id }}"
                                            {{-- Check if this ID was in the old input after a validation error --}}
                                            @checked(is_array(old('prerequisites')) && in_array($prereqCourse->id, old('prerequisites')))
                                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $prereqCourse->title }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No other courses exist to be set as a prerequisite.</p>
                                @endforelse
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('prerequisites')" />
                        </div>
                        {{-- Duration --}}
                        <div>
                            <x-input-label for="duration_months" :value="__('Duration (Months, Optional)')" />
                            <x-text-input id="duration_months" name="duration_months" type="number" min="1" class="mt-1 block w-full" :value="old('duration_months')" />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_months')" />
                        </div>

                        {{-- Exam Date --}}
                        <div>
                            <x-input-label for="final_exam_date" :value="__('Final Exam Date (Optional)')" />
                            {{-- Use datetime-local for user convenience, but validation will handle format --}}
                            <x-text-input id="final_exam_date" name="final_exam_date" type="datetime-local" class="mt-1 block w-full" :value="old('final_exam_date')" />
                            <x-input-error class="mt-2" :messages="$errors->get('final_exam_date')" />
                        </div>

                        {{-- Passing Grade --}}
                        <div>
                            <x-input-label for="passing_grade" :value="__('Passing Grade % (Optional)')" />
                            <x-text-input id="passing_grade" name="passing_grade" type="number" min="0" max="100" class="mt-1 block w-full" :value="old('passing_grade')" />
                            <x-input-error class="mt-2" :messages="$errors->get('passing_grade')" />
                        </div>

                        {{-- Certificate Template Path (Simple text input for now) --}}
                        <div>
                            <x-input-label for="certificate_template_path" :value="__('Certificate Template Path (Optional)')" />
                            <x-text-input id="certificate_template_path" name="certificate_template_path" type="text" class="mt-1 block w-full" :value="old('certificate_template_path')" />
                            <x-input-error class="mt-2" :messages="$errors->get('certificate_template_path')" />
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create Course') }}</x-primary-button>
                             <a href="{{ route('pengelola.courses.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
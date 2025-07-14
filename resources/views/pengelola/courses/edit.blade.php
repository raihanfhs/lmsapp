<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Course') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('pengelola.courses.update', $course->id) }}" class="space-y-6">
                        @csrf       {{-- CSRF Protection --}}
                        @method('PUT') {{-- Specify PUT method for updates --}}

                        {{-- Title --}}
                        <div>
                            <x-input-label for="title" :value="__('Course Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full dark:text-gray-900" :value="old('title', $course->title)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- Course Code --}}
                        <div>
                            <x-input-label for="course_code" :value="__('Course Code (Optional)')" />
                            <x-text-input id="course_code" name="course_code" type="text" class="mt-1 block w-full" :value="old('course_code', $course->course_code)" />
                            <x-input-error class="mt-2" :messages="$errors->get('course_code')" />
                        </div>

                        {{-- Description --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $course->description) }}</textarea>
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
                                            name="prerequisites[]"
                                            type="checkbox"
                                            value="{{ $prereqCourse->id }}"
                                            {{-- Check if this ID was in old input OR is an existing prerequisite --}}
                                            @checked(in_array($prereqCourse->id, old('prerequisites', $prerequisiteIds)))
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
                            <x-text-input id="duration_months" name="duration_months" type="number" min="1" class="mt-1 block w-full" :value="old('duration_months', $course->duration_months)" />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_months')" />
                        </div>

                        {{-- Exam Date --}}
                        <div>
                            <x-input-label for="final_exam_date" :value="__('Final Exam Date (Optional)')" />
                            {{-- Format date/time correctly for the datetime-local input --}}
                            <x-text-input id="final_exam_date" name="final_exam_date" type="datetime-local" class="mt-1 block w-full"
                                          :value="old('final_exam_date', $course->final_exam_date ? $course->final_exam_date->format('Y-m-d\TH:i') : '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('final_exam_date')" />
                        </div>

                        {{-- Passing Grade --}}
                        <div>
                            <x-input-label for="passing_grade" :value="__('Passing Grade % (Optional)')" />
                            <x-text-input id="passing_grade" name="passing_grade" type="number" min="0" max="100" class="mt-1 block w-full" :value="old('passing_grade', $course->passing_grade)" />
                            <x-input-error class="mt-2" :messages="$errors->get('passing_grade')" />
                        </div>

                        {{-- Certificate Template Path --}}
                        <div class="mt-4">
                            <label for="certificate_template_id" class="block font-medium text-sm text-gray-700">
                                Template Sertifikat (Opsional)
                            </label>
                            <select name="certificate_template_id" id="certificate_template_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- Tidak Ada Sertifikat --</option>
                                @foreach ($certificateTemplates as $template)
                                    <option value="{{ $template->id }}" @selected(old('certificate_template_id', $course->certificate_template_id) == $template->id)>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">
                                Pilih template yang akan digunakan saat siswa menyelesaikan kursus ini.
                            </p>
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Course') }}</x-primary-button>
                             <a href="{{ route('pengelola.courses.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
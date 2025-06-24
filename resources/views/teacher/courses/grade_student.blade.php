<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Enter Grade for') }} {{ $student->name }} - {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('teacher.courses.enrollments.grade.store', ['course' => $course->id, 'user' => $student->id]) }}">
                        @csrf

                        <div>
                            <x-input-label for="grade" :value="__('Grade (0-100)')" />
                            <x-text-input id="grade" name="grade" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('grade', $existingGrade?->grade)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('grade')" />
                            @if($course->passing_grade)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Passing Grade for this course: {{ $course->passing_grade }}%</p>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>{{ __('Save Grade') }}</x-primary-button>
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>

                    @if($existingGrade)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Recorded Grade: {{ $existingGrade->grade }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:
                                @if($existingGrade->passed)
                                    <span class="text-green-600">Passed</span>
                                @else
                                    <span class="text-red-600">Not Passed</span>
                                @endif
                            </p>
                            @php
                                $certificate = $student->certificates()->where('course_id', $course->id)->first();
                            @endphp
                            @if($certificate)
                            <p class="text-sm font-medium text-green-700 dark:text-green-300">Certificate Issued: {{ $certificate->issue_date->format('M d, Y') }} (Code: {{ $certificate->unique_code }})</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
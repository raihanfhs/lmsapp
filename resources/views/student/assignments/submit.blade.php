<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit Assignment: ') }} <span class="text-blue-600">{{ $assignment->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">

                    {{-- Assignment Details --}}
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assignment Instructions</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Due Date:</strong> {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') : 'No due date' }}
                        </p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Total Points:</strong> {{ $assignment->total_points }}
                        </p>
                        <div class="mt-4 prose dark:prose-invert max-w-none">
                            <p>{{ $assignment->description }}</p>
                        </div>
                    </div>

                    {{-- Submission Form --}}
                    <form method="POST" action="{{ route('student.assignments.submission.store', $assignment) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- File Upload -->
                        <div>
                            <x-input-label for="submission_file" :value="__('Upload Your Work')" />
                            <input id="submission_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="submission_file" required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="file_input_help">PDF, DOC, DOCX, ZIP, JPG, or PNG (Max: 10MB).</p>
                            <x-input-error :messages="$errors->get('submission_file')" class="mt-2" />
                        </div>

                        <!-- Comments -->
                        <div class="mt-6">
                            <x-input-label for="student_comments" :value="__('Comments (Optional)')" />
                            <textarea id="student_comments" name="student_comments" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" rows="4">{{ old('student_comments') }}</textarea>
                            <x-input-error :messages="$errors->get('student_comments')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('student.courses.show', $assignment->course_id) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Submit My Work') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

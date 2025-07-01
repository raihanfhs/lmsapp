<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $course->title }}
            </h2>
            <a href="{{ route('student.courses.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                &larr; Back to My Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- KOLOM KIRI (Konten Utama) --}}
                <div class="md:col-span-2 space-y-6">
                    
                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">About This Course</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $course->description ?? 'No description provided.' }}
                        </p>
                    </x-card>

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Materials</h3>
                        <div class="space-y-2">
                            @forelse ($course->materials as $material)
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $material->title }}</h4>
                                    <div class="prose dark:prose-invert max-w-none mt-2 text-gray-600 dark:text-gray-400">
                                        {{-- Asumsi materi disimpan sebagai teks biasa, jika HTML gunakan {!! !!} --}}
                                        <p>{{ $material->description }}</p>
                                        @if($material->file_path)
                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Download Material</a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">The teacher has not added any materials to this course yet.</p>
                            @endforelse
                        </div>
                    </x-card>

                    {{-- Card untuk Kuis --}}
                    <div class="mt-6">
                        <x-card>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Quizzes') }}
                            </h3>
                            <div class="space-y-4">
                                @forelse ($course->quizzes as $quiz)
                                    @php
                                        // Count how many times the student has attempted THIS specific quiz
                                        $attemptsCount = $student->quizAttempts->where('quiz_id', $quiz->id)->count();
                                        // Check if the student is allowed to take the quiz again
                                        $canTakeQuiz = is_null($quiz->max_attempts) || $attemptsCount < $quiz->max_attempts;
                                    @endphp

                                    <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->title }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $quiz->questions_count ?? $quiz->questions->count() }} Questions | {{ $quiz->duration }} minutes
                                            </p>
                                            {{-- Display attempt counter if a limit is set --}}
                                            @if ($quiz->max_attempts)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    (Attempts: {{ $attemptsCount }} / {{ $quiz->max_attempts }})
                                                </p>
                                            @endif
                                        </div>
                                        <div>
                                            @if ($canTakeQuiz)
                                                <form method="POST" action="{{ route('student.quizzes.start_attempt', $quiz) }}">
                                                    @csrf
                                                    <x-primary-button type="submit">
                                                        {{ __('Start Quiz') }}
                                                    </x-primary-button>
                                                </form>
                                            @else
                                                <x-primary-button disabled class="bg-gray-400 cursor-not-allowed">
                                                    No Attempts Left
                                                </x-primary-button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">There are no quizzes available for this course at the moment.</p>
                                @endforelse
                            </div>
                        </x-card>
                    </div>

                    <div class="mt-6">
                        <x-card>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Assignments') }}
                            </h3>
                            <div class="space-y-4">
                                @forelse ($course->assignments as $assignment)
                                    @php
                                        // Check if the current student has a submission for this specific assignment
                                        $submission = $student->assignmentSubmissions->firstWhere('assignment_id', $assignment->id);
                                    @endphp
                                    <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $assignment->title }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Due: {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') : 'No due date' }} | Points: {{ $assignment->total_points }}
                                            </p>
                                        </div>
                                        <div>
                                            @if ($submission)
                                                <span class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded-full">
                                                    Submitted
                                                </span>
                                            @else
                                                {{-- We will create this route in the next step --}}
                                                <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                    Submit Work
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">There are no assignments for this course yet.</p>
                                @endforelse
                            </div>
                        </x-card>
                    </div>
                </div>

                {{-- KOLOM KANAN (Sidebar Info) --}}
                <div class="md:col-span-1 space-y-6">

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Information</h3>
                        <dl>
                            <div class="mb-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teacher(s)</dt>
                                <dd class="text-gray-900 dark:text-gray-100">
                                    {{-- INI ADALAH BARIS YANG DIPERBAIKI --}}
                                    {{ $course->teachers->pluck('name')->join(', ') }}
                                </dd>
                            </div>
                            <div class="mb-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course Code</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->course_code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->duration_months ?? 'N/A' }} Months</dd>
                            </div>
                        </dl>
                    </x-card>

                    <x-card>
                         <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">My Progress</h3>
                         <p class="text-gray-500 dark:text-gray-400">Your final grade and certificate information will appear here.</p>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
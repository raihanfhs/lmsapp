{{-- resources/views/student/courses/show.blade.php --}}

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
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    {{-- Tab Materi --}}
                    <a href="{{ route('student.courses.show', $course->id) }}"
                    class="{{ request()->routeIs('student.courses.show') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Materi
                    </a>

                    {{-- Tab Diskusi --}}
                    <a href="{{ route('forum.index', $course->id) }}"
                    class="{{ request()->routeIs('forum.index') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Diskusi
                    </a>

                    {{-- Tab Tugas (Contoh) --}}
                    {{-- Anda perlu memastikan route 'student.assignments.index' sudah ada dan menerima parameter course --}}
                    {{-- <a href="#"
                    class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Tugas
                    </a> --}}

                    {{-- Tab Kuis (Contoh) --}}
                    {{-- Anda perlu memastikan route 'student.quizzes.index' sudah ada dan menerima parameter course --}}
                    {{-- <a href="#"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Kuis
                    </a> --}}
                </nav>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- KOLOM KIRI (Course Content organized by Sections) --}}
                <div class="md:col-span-2 space-y-6">

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">About This Course</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $course->description ?? 'No description provided.' }}
                        </p>
                    </x-card>

                    {{-- Course Sections (Modules/Chapters) --}}
                    @forelse ($course->sections as $section)
                        <x-card>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $section->title }}</h3>

                            {{-- Materials within this Section --}}
                            @if($section->materials->isNotEmpty())
                                <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Materials</h4>
                                <div class="space-y-3 mb-4">
                                    @foreach ($section->materials as $material)
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md border border-gray-100 dark:border-gray-600">
                                            <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $material->title }}</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $material->description }}</p>
                                            <div class="mt-2 text-sm">
                                                @if($material->type === 'video_url')
                                                    <a href="{{ $material->content }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197 2.132A1 1 0 0110 13.805v-3.61c0-.81 1.298-1.074 2.062-.518l3.197 2.132a1 1 0 010 1.636z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Watch Video
                                                    </a>
                                                @elseif($material->content)
                                                    <a href="{{ Storage::url($material->content) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                        Download {{ ucfirst(str_replace('_file', '', $material->type)) }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Quizzes within this Section --}}
                            @if($section->quizzes->isNotEmpty())
                                <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Quizzes</h4>
                                <div class="space-y-3 mb-4">
                                    @foreach ($section->quizzes as $quiz)
                                        @php
                                            $attemptsCount = $student->quizAttempts->where('quiz_id', $quiz->id)->count();
                                            $canTakeQuiz = is_null($quiz->max_attempts) || $attemptsCount < $quiz->max_attempts;
                                        @endphp
                                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->title }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $quiz->questions_count ?? $quiz->questions->count() }} Questions | {{ $quiz->duration }} minutes
                                                    @if ($quiz->max_attempts)
                                                        (Attempts: {{ $attemptsCount }} / {{ $quiz->max_attempts }})
                                                    @endif
                                                </p>
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
                                    @endforeach
                                </div>
                            @endif

                            {{-- Assignments within this Section --}}
                            @if($section->assignments->isNotEmpty())
                                <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Assignments</h4>
                                <div class="space-y-3 mb-4">
                                    @foreach ($section->assignments as $assignment)
                                        @php
                                            $submission = $student->assignmentSubmissions->firstWhere('assignment_id', $assignment->id);
                                        @endphp
                                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
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
                                                    <a href="{{ route('student.assignments.submission.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                        Submit Work
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($section->materials->isEmpty() && $section->quizzes->isEmpty() && $section->assignments->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400">No content available in this section yet.</p>
                            @endif
                        </x-card>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">The teacher has not organized content into sections yet. Materials, quizzes, and assignments will appear below.</p>
                        
                        {{-- Fallback for un-sectioned content --}}
                        @if($course->materials->isNotEmpty() || $course->quizzes->isNotEmpty() || $course->assignments->isNotEmpty())
                            <x-card>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Uncategorized Content</h3>
                                {{-- Display original materials list if no sections --}}
                                @if($course->materials->isNotEmpty())
                                    <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-2">Materials</h4>
                                    <div class="space-y-2 mb-4">
                                        @foreach ($course->materials as $material)
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md border border-gray-100 dark:border-gray-600">
                                                <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $material->title }}</h5>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $material->description }}</p>
                                                <div class="mt-2 text-sm">
                                                    @if($material->type === 'video_url')
                                                        <a href="{{ $material->content }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197 2.132A1 1 0 0110 13.805v-3.61c0-.81 1.298-1.074 2.062-.518l3.197 2.132a1 1 0 010 1.636z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            Watch Video
                                                        </a>
                                                    @elseif($material->content)
                                                        <a href="{{ Storage::url($material->content) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                            Download {{ ucfirst(str_replace('_file', '', $material->type)) }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                {{-- Display original quizzes list if no sections --}}
                                @if($course->quizzes->isNotEmpty())
                                    <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-2">Quizzes</h4>
                                    <div class="space-y-2 mb-4">
                                        @foreach ($course->quizzes as $quiz)
                                            @php
                                                $attemptsCount = $student->quizAttempts->where('quiz_id', $quiz->id)->count();
                                                $canTakeQuiz = is_null($quiz->max_attempts) || $attemptsCount < $quiz->max_attempts;
                                            @endphp
                                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                                                <div>
                                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->title }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $quiz->questions_count ?? $quiz->questions->count() }} Questions | {{ $quiz->duration }} minutes
                                                        @if ($quiz->max_attempts)
                                                            (Attempts: {{ $attemptsCount }} / {{ $quiz->max_attempts }})
                                                        @endif
                                                    </p>
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
                                        @endforeach
                                    </div>
                                @endif
                                {{-- Display original assignments list if no sections --}}
                                @if($course->assignments->isNotEmpty())
                                    <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-2">Assignments</h4>
                                    <div class="space-y-2 mb-4">
                                        @foreach ($course->assignments as $assignment)
                                            @php
                                                $submission = $student->assignmentSubmissions->firstWhere('assignment_id', $assignment->id);
                                            @endphp
                                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
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
                                                        <a href="{{ route('student.assignments.submission.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                            Submit Work
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </x-card>
                        @endif
                    @endforelse

                    {{-- Meetings Section (assuming meetings are course-wide, not section-specific) --}}
                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Scheduled Meetings</h3>
                        <div class="space-y-4">
                            @forelse ($course->meetings as $meeting)
                                <div class="flex justify-between items-start p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md border border-gray-100 dark:border-gray-600">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $meeting->title }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($meeting->meeting_datetime)->format('D, d M Y - H:i') }}
                                        </p>
                                        @if ($meeting->type === 'online')
                                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197 2.132A1 1 0 0110 13.805v-3.61c0-.81 1.298-1.074 2.062-.518l3.197 2.132a1 1 0 010 1.636z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Join Meeting
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                                Location: {{ $meeting->location }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No meetings have been scheduled for this course yet.</p>
                            @endforelse
                        </div>
                    </x-card>

                </div>

                {{-- KOLOM KANAN (Sidebar Info) --}}
                <div class="md:col-span-1 space-y-6">

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Information</h3>
                        <dl>
                            <div class="mb-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teacher(s)</dt>
                                <dd class="text-gray-900 dark:text-gray-100">
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
                         {{-- You can add dynamic content here like: --}}
                         {{-- @if($studentGrade = $student->studentGrades->firstWhere('course_id', $course->id))
                            <p class="mt-2 text-md text-gray-700">Grade: {{ $studentGrade->grade }}%</p>
                            <p class="text-md {{ $studentGrade->passed ? 'text-green-600' : 'text-red-600' }}">Status: {{ $studentGrade->passed ? 'Passed' : 'Failed' }}</p>
                            @if($certificate = $student->certificates->firstWhere('course_id', $course->id))
                                <p class="mt-2 text-sm text-green-700">Certificate Issued: <a href="{{ Storage::url($certificate->certificate_path) }}" target="_blank" class="underline">View Certificate</a></p>
                            @endif
                         @else
                            <p class="mt-2 text-sm text-gray-500">No grade recorded yet.</p>
                         @endif --}}
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
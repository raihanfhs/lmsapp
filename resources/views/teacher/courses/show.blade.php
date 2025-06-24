<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{-- Course Title --}}
                {{ $course->title }}
            </h2>
            <a href="{{ route('teacher.courses.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                &larr; Back to All Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- Grid Layout --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Main Content Column --}}
                <div class="md:col-span-2 space-y-6">
                    
                    <x-card> 
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">About This Course</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $course->description ?? 'No description provided.' }}
                        </p>
                    </x-card>

                    <x-card>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Course Materials</h3>
                            <a href="{{ route('teacher.courses.materials.create', ['course' => $course->id]) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">Add Material</a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($course->materials->whereNull('parent_id') as $material)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $material->title }}</span>
                                        <div>
                                            <a href="{{ route('teacher.courses.materials.edit', ['course' => $course->id, 'material' => $material->id]) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 mr-3 text-sm">Edit</a>
                                            <form action="{{ route('teacher.courses.materials.destroy', ['course' => $course->id, 'material' => $material->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:text-red-500 text-sm">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- Display child materials --}}
                                    @if($material->children->isNotEmpty())
                                        <div class="mt-2 pl-6 border-l-2 border-gray-200 dark:border-gray-600 space-y-2">
                                            @foreach($material->children as $child)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $child->title }}</span>
                                                    <div>
                                                        <a href="{{ route('teacher.courses.materials.edit', ['course' => $course->id, 'material' => $child->id]) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 mr-3 text-sm">Edit</a>
                                                        <form action="{{ route('teacher.courses.materials.destroy', ['course' => $course->id, 'material' => $child->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:text-red-500 text-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No materials have been added to this course yet.</p>
                            @endforelse
                        </div>
                    </x-card>

                    <x-card>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quizzes</h3>
                            <a href="{{ route('teacher.quizzes.create', $course) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('Create New Quiz') }}
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($course->quizzes as $quiz)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                    <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="text-gray-800 dark:text-gray-200 hover:underline">
                                        {{ $quiz->title }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        {{ $quiz->questions_count }} {{ Str::plural('Question', $quiz->questions_count) }} | {{ $quiz->duration }} min
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No quizzes have been created for this course yet.</p>
                            @endforelse
                        </div>
                    </x-card>

                </div>

                {{-- Sidebar Column --}}
                <div class="md:col-span-1 space-y-6">

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Info</h3>
                        <dl>
                            <div class="mb-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course Code</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->course_code ?? 'N/A' }}</dd>
                            </div>
                            <div class="mb-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->duration_months ? $course->duration_months . ' Months' : 'N/A' }}</dd>
                            </div>
                        </dl>
                    </x-card>

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Enrolled Students & Grading</h3>
                        <div class="space-y-3">
                            @forelse ($course->enrolledStudents as $student)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-800 dark:text-gray-200">{{ $student->name }}</span>
                                    <a href="{{ route('teacher.courses.enrollments.grade.form', ['course' => $course->id, 'user' => $student->id]) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                        Enter Grade
                                    </a>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No students are currently enrolled.</p>
                            @endforelse
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
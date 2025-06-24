<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Browse Available Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Session Success Message --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-600 dark:text-green-300" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            {{-- Session Error Message --}}
            @if (session('error'))
                 <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-600 dark:text-red-300" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($courses->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($courses as $course)
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $course->title }}</h3>
                                    @if($course->course_code)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $course->course_code }}</p>
                                    @endif
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                        {{ Str::limit($course->description, 100) }} {{-- Show a limited description --}}
                                    </p>
                                    @if($course->duration_months)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Duration: {{ $course->duration_months }} Months
                                        </p>
                                    @endif
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Teacher(s):
                                        @forelse($course->teachers as $teacher)
                                            {{ $teacher->name }}{{ !$loop->last ? ', ' : '' }}
                                        @empty
                                            N/A
                                        @endforelse
                                    </p>

                                    <div class="mt-4">
                                        {{-- Check if student is already enrolled --}}
                                        @php
                                            $isEnrolled = Auth::user()->enrolledCourses->contains($course->id);
                                        @endphp

                                        @if ($isEnrolled)
                                            <span class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest cursor-not-allowed">
                                                Already Enrolled
                                            </span>
                                            <a href="{{ route('student.courses.show', $course->id) }}" class="ml-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View Course</a>
                                        @else
                                            <form method="POST" action="{{ route('student.enrollments.store', $course->id) }}" class="inline">
                                                @csrf
                                                <x-primary-button>
                                                    {{ __('Enroll Now') }}
                                                </x-primary-button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $courses->links() }} {{-- Pagination links --}}
                        </div>
                    @else
                        <p>No courses available at the moment. Please check back later!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
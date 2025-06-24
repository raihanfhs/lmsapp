<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 dark:text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="mt-2 text-gray-600">
                        You are currently enrolled in <b>{{ $enrolledLearningPaths->count() }} Learning Path(s)</b>,
                        covering a total of <b>{{ $enrolledCoursesCount }} course(s)</b>.
                        Keep up the great work!
                    </p>
                </div>
            </div>
            <div class="mt-6"> 
                @if(session('success'))
                    <div class="rounded-lg bg-emerald-100 p-4 text-sm text-emerald-700" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            @if(auth()->user()->hasRole('student') && $enrolledLearningPaths->isNotEmpty())
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800">My Learning Paths</h3>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                            @foreach ($enrolledLearningPaths as $path)
                                <div class="border border-gray-200 rounded-lg p-4 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-900">{{ $path->title }}</h4>

                                        <p class="text-sm text-gray-600 mt-1">{{ $path->courses_count }} Courses</p>

                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('student.learning-paths.show', $path) }}" class="w-full text-center inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                            Continue Learning
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $learningPath->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('info'))
                <div class="rounded-lg bg-blue-100 p-4 text-sm text-blue-700" role="alert">
                    {{ session('info') }}
                </div>
            @endif  @if(session('success'))
                <div class="rounded-lg bg-emerald-100 p-4 text-sm text-emerald-700" role="alert">
                    {{ session('success') }}
                </div>
            @endif  </div>
    </div>
    <div class="pt-6 sm:px-6 lg:px-8"> <div class="max-w-7xl mx-auto">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <div class="md:col-span-2">
                            <h3 class="text-2xl font-bold text-gray-900">Courses in this Path</h3>
                            <div class="mt-4 space-y-5">
                                @forelse ($learningPath->courses as $index => $course)
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500 text-white font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-800">{{ $course->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $course->short_description ?? 'No description available.' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500">No courses have been added to this path yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="md:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-xl font-bold text-gray-900">About this Path</h3>
                                <p class="mt-4 text-sm text-gray-600">
                                    {{ $learningPath->description }}
                                </p>
                                <div class="mt-6">
                                    <form method="POST" action="{{ route('student.learning-paths.enroll', $learningPath) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            Enroll Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
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
                            @forelse ($courses as $course)
                                <x-card>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $course->title }}</h3>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ Str::limit($course->description, 100) }}
                                    </p>
                                    <div class="mt-4">
                                        <form action="{{ route('student.enrollments.store', $course) }}" method="POST">
                                            @csrf
                                            <x-primary-button>Enroll Now</x-primary-button>
                                        </form>
                                    </div>
                                </x-card>
                            @empty
                                {{-- This message will show if the $courses collection is empty --}}
                                <div class="md:col-span-3 text-center py-12">
                                    <p class="text-gray-500 dark:text-gray-400">There are currently no courses available for enrollment. Please check back later!</p>
                                </div>
                            @endforelse
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
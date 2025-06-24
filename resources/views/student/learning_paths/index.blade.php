<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Learning Paths') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($learningPaths as $path)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                        <div class="p-6 text-gray-900 flex-grow">
                            <h3 class="font-semibold text-lg">{{ $path->title }}</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                {{ Str::limit($path->description, 100) }}
                            </p>
                        </div>
                        <div class="px-6 pb-4 border-t border-gray-200 flex justify-between items-center">
                           <span class="text-sm text-gray-500">{{ $path->courses_count }} Courses</span>
                        <a href="{{ route('student.learning-paths.show', $path) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            View Path
                        </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500">No learning paths available at the moment.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $learningPaths->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
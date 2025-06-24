{{-- File: resources/views/pengelola/courses/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Content: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
             @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <x-card>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Course Materials</h3>
                    {{-- THIS IS THE BUTTON YOU ARE LOOKING FOR --}}
                    <a href="{{ route('pengelola.courses.materials.create', ['course' => $course->id]) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">Add Material</a>
                </div>
                <div class="space-y-4">
                    @forelse ($course->materials->whereNull('parent_id') as $material)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $material->title }}</span>
                                <div>
                                    <a href="{{ route('pengelola.courses.materials.edit', ['course' => $course->id, 'material' => $material->id]) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 mr-3 text-sm">Edit</a>
                                    <form action="{{ route('pengelola.courses.materials.destroy', ['course' => $course->id, 'material' => $material->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:text-red-500 text-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">No materials have been added to this course yet.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
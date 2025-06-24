<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Course') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add New Section</h3>
                    <form action="{{ route('teacher.courses.sections.store', $course) }}" method="POST" class="mt-6 space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="title" :value="__('Section Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Section') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Content</h3>
                <div class="space-y-4">
                    @forelse ($course->sections as $section)
                        <div class="p-4 border dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-center">
                                <h4 class="text-md font-semibold dark:text-white">{{ $section->title }}</h4>
                                <div class="flex items-center space-x-2">
                                    <form action="{{ route('sections.destroy', ['course' => $course, 'section' => $section]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button type="submit" onclick="return confirm('Are you sure you want to delete this section and all its materials?')">Delete Section</x-danger-button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 pl-4 border-l-2 dark:border-gray-600 space-y-2">
                                @forelse ($section->materials as $material)
                                    <div class="flex justify-between items-center group">
                                        <span class="dark:text-gray-300">{{ $material->title }}</span>
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('materials.edit', $material->id) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline text-sm">Edit</a>
                                            
                                            <form action="{{ route('materials.destroy', $material->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:underline text-sm" onclick="return confirm('Are you sure you want to delete this material?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No materials in this section yet.</p>
                                @endforelse
                            </div>
                            
                            <div class="mt-4">
                               <a href="{{ route('teacher.courses.materials.create', ['course' => $course, 'section_id' => $section->id]) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                    + Add Material to this Section
                                </a>
                            </div>

                        </div>
                    @empty
                        <p class="dark:text-gray-300">No sections have been created for this course yet.</p>
                    @endforelse
                </div>
            </div>
            </div>
    </div>
</x-app-layout>
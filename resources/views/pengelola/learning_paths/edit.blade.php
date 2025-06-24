<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Learning Path') }} </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('pengelola.learning-paths.update', $learningPath) }}">
                        @csrf
                        @method('PUT') <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $learningPath->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $learningPath->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="is_active" :value="__('Status')" />
                            <select name="is_active" id="is_active" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="1" {{ old('is_active', $learningPath->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $learningPath->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Manage Courses in this Path
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Select the courses that should be included in this learning path.
                                </p>
                                <div class="mt-4 space-y-4">
                                    @forelse ($courses as $course)
                                        <div class="flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input
                                                    id="course_{{ $course->id }}"
                                                    name="courses[]"
                                                    type="checkbox"
                                                    value="{{ $course->id }}"
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                    {{ in_array($course->id, $assignedCourseIds) ? 'checked' : '' }}
                                                >
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="course_{{ $course->id }}" class="font-medium text-gray-900">{{ $course->title }}</label>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">No courses available. Please create a course first.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengelola.learning-paths.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Learning Path') }} </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
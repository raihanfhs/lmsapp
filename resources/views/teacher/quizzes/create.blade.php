<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Quiz for: ') }} <span class="text-blue-600">{{ $course->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('teacher.quizzes.store', $course) }}">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Quiz Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="duration" :value="__('Duration (in minutes)')" />
                            <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration')" required />
                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="passing_grade" :value="__('Passing Grade (%)')" />
                            <x-text-input id="passing_grade" class="block mt-1 w-full" type="number" name="passing_grade" :value="old('passing_grade', 70)" min="0" max="100" required />
                            <x-input-error :messages="$errors->get('passing_grade')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="max_attempts" :value="__('Max Attempts (Leave blank for unlimited)')" />
                            <x-text-input id="max_attempts" class="block mt-1 w-full" type="number" name="max_attempts" :value="old('max_attempts')" min="1" />
                            <x-input-error :messages="$errors->get('max_attempts')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('teacher.quizzes.index', $course) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Save and Add Questions') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        {{-- Change Title --}}
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Quiz: ') }} <span class="text-blue-600">{{ $quiz->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- 1. Change form action and add @method('PUT') --}}
                    <form method="POST" action="{{ route('teacher.quizzes.update', ['course' => $course, 'quiz' => $quiz]) }}">
                        @csrf
                        @method('PUT')

                        {{-- 2. Update :value for title --}}
                        <div>
                            <x-input-label for="title" :value="__('Quiz Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $quiz->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        {{-- 3. Update value for description --}}
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $quiz->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- 4. Update :value for duration --}}
                        <div class="mt-4">
                            <x-input-label for="duration" :value="__('Duration (in minutes)')" />
                            <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration', $quiz->duration)" required />
                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                        </div>

                         {{-- 5. Update :value for pass_grade and fix name --}}
                        <div class="mt-4">
                            <x-input-label for="pass_grade" :value="__('Passing Grade (%)')" />
                            <x-text-input id="pass_grade" class="block mt-1 w-full" type="number" name="pass_grade" :value="old('pass_grade', $quiz->pass_grade)" min="0" max="100" required />
                            <x-input-error :messages="$errors->get('pass_grade')" class="mt-2" />
                        </div>

                        {{-- 6. Add the new Max Attempts field with the existing value --}}
                        <div class="mt-4">
                            <x-input-label for="max_attempts" :value="__('Max Attempts (Leave blank for unlimited)')" />
                            <x-text-input id="max_attempts" class="block mt-1 w-full" type="number" name="max_attempts" :value="old('max_attempts', $quiz->max_attempts)" min="1" />
                            <x-input-error :messages="$errors->get('max_attempts')" class="mt-2" />
                        </div>

                        {{-- 7. Change button text --}}
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('teacher.quizzes.index', $course) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Quiz') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
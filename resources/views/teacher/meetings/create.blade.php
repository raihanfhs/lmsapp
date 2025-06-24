<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Schedule New Online Meeting for Course') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('teacher.courses.meetings.store', $course->id) }}" class="space-y-6">
                        @csrf

                        {{-- Meeting Title --}}
                        <div>
                            <x-input-label for="title" :value="__('Meeting Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- Meeting Date & Time --}}
                        <div>
                            <x-input-label for="meeting_datetime" :value="__('Meeting Date & Time')" />
                            <x-text-input id="meeting_datetime" name="meeting_datetime" type="datetime-local" class="mt-1 block w-full" :value="old('meeting_datetime')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('meeting_datetime')" />
                        </div>

                        {{-- Meeting Link (Zoom, GMeet, etc.) --}}
                        <div>
                            <x-input-label for="meeting_link" :value="__('Meeting Link (e.g., Zoom, Google Meet URL)')" />
                            <x-text-input id="meeting_link" name="meeting_link" type="url" class="mt-1 block w-full" :value="old('meeting_link')" required placeholder="https://zoom.us/j/..." />
                            <x-input-error class="mt-2" :messages="$errors->get('meeting_link')" />
                        </div>

                        {{-- Description (Optional) --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Schedule Meeting') }}</x-primary-button>
                             <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
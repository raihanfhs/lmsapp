<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Meeting for: ') }} {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Inisialisasi Alpine.js dengan data yang sudah ada --}}
                    <form method="POST" action="{{ route('teacher.courses.meetings.update', ['course' => $course, 'meeting' => $meeting]) }}" x-data="{ type: '{{ old('type', $meeting->type) }}' }">
                        @csrf
                        @method('PUT') {{-- Gunakan method PUT untuk update --}}

                        <div>
                            <x-input-label for="title" :value="__('Meeting Title')" />
                            {{-- Gunakan old() untuk menjaga input jika validasi gagal, dan $meeting->title sebagai default --}}
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $meeting->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label :value="__('Meeting Type')" />
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="online" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2">Online</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="offline" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2">Offline</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mt-4" x-show="type === 'online'">
                            <x-input-label for="meeting_link" :value="__('Meeting Link (e.g., Zoom, Google Meet)')" />
                            <x-text-input id="meeting_link" class="block mt-1 w-full" type="url" name="meeting_link" :value="old('meeting_link', $meeting->meeting_link)" />
                            <x-input-error :messages="$errors->get('meeting_link')" class="mt-2" />
                        </div>

                        <div class="mt-4" x-show="type === 'offline'">
                            <x-input-label for="location" :value="__('Location (e.g., Room Name, Address)')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $meeting->location)" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="meeting_datetime" :value="__('Date and Time')" />
                            {{-- Format tanggal agar sesuai dengan input datetime-local --}}
                            <x-text-input id="meeting_datetime" class="block mt-1 w-full" type="datetime-local" name="meeting_datetime" :value="old('meeting_datetime', \Carbon\Carbon::parse($meeting->meeting_datetime)->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('meeting_datetime')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea name="description" id="description" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700">{{ old('description', $meeting->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Update Meeting') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
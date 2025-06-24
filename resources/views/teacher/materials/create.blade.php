<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Material to Course') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                {{-- We use Alpine.js to manage the form's state --}}
                <div x-data="{ type: 'video_url' }">
                    <form method="POST" action="{{ route('teacher.courses.materials.store', $course->id) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf

                        {{-- Material Type Selection --}}
                        <div>
                            <x-input-label for="type" :value="__('Material Type')" />
                            <select id="type" name="type" x-model="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="video_url">Video URL (e.g., YouTube)</option>
                                <option value="document_file">Document (PDF, Docx)</option>
                                <option value="image_file">Image (PNG, JPG)</option>
                            </select>
                        </div>

                        {{-- Title --}}
                        <div>
                            <x-input-label for="title" :value="__('Material Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- Description --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        {{-- Dynamic Input for Content --}}
                        {{-- This input shows only if the type is 'video_url' --}}
                        <div x-show="type === 'video_url'">
                            <x-input-label for="video_url" :value="__('Video URL')" />
                            <x-text-input id="video_url" name="content_url" type="url" class="mt-1 block w-full" placeholder="https://www.youtube.com/watch?v=..." :value="old('content_url')" />
                            <x-input-error class="mt-2" :messages="$errors->get('content_url')" />
                        </div>

                        {{-- This input shows for any file type --}}
                        <div x-show="type.includes('_file')">
                            <x-input-label for="file_upload" :value="__('Upload File')" />
                            <input id="file_upload" name="content_file" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            <x-input-error class="mt-2" :messages="$errors->get('content_file')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Material') }}</x-primary-button>
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
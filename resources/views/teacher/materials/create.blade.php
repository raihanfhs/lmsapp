<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Material to Course') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Ensure form can handle file uploads --}}
                    <form method="POST" action="{{ route('teacher.courses.materials.store', $course->id) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf

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
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        {{-- Parent Material (for Hierarchy) --}}
                        <div>
                            <x-input-label for="parent_id" :value="__('Parent Material (Optional - Leave blank for Top Level)')" />
                            <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- None (Top Level) --</option>
                                @foreach ($parentMaterials as $material)
                                    <option value="{{ $material->id }}" @selected(old('parent_id') == $material->id)>
                                        {{ $material->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
                        </div>

                         {{-- Video File Upload --}}
                        <div>
                            <x-input-label for="video_file" :value="__('Video File')" />
                            <input id="video_file" name="video_file" type="file" required class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" accept="video/mp4,video/mpeg,video/quicktime,video/webm,video/x-ms-wmv,video/avi">
                            {{-- Suggest common video types --}}
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">MP4, MOV, AVI, WMV, WEBM etc. (Max Upload Size: {{ ini_get('upload_max_filesize') }}B)</p>
                            <x-input-error class="mt-2" :messages="$errors->get('video_file')" />
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Upload Material') }}</x-primary-button>
                            {{-- Adjust cancel link destination as needed --}}
                             <a href="{{ route('teacher.courses.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
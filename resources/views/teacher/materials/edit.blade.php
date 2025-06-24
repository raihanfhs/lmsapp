<x-app-layout>
    <x-slot name="header">
         <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Material') }}: {{ $material->title }}
            </h2>
             <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                {{ __('Back to Course Materials') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Note: No enctype needed as we are not uploading files here --}}
                    <form method="POST" action="{{ route('teacher.courses.materials.update', [$course->id, $material->id]) }}" class="space-y-6">
                        @csrf
                        @method('PUT') {{-- Method for updating --}}

                        {{-- Title --}}
                        <div>
                            <x-input-label for="title" :value="__('Material Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $material->title)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- Description --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $material->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        {{-- Parent Material (for Hierarchy) --}}
                        <div>
                            <x-input-label for="parent_id" :value="__('Parent Material (Optional - Leave blank for Top Level)')" />
                            <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- None (Top Level) --</option>
                                @foreach ($parentMaterials as $parentMat)
                                    <option value="{{ $parentMat->id }}" @selected(old('parent_id', $material->parent_id) == $parentMat->id)>
                                        {{ $parentMat->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
                        </div>

                        {{-- Display Current File (No Upload) --}}
                         <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Current File:</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                @if($material->file_path)
                                    {{ basename($material->file_path) }}
                                    <span class="text-xs italic ml-2">(File cannot be changed during edit. Delete and re-upload if needed.)</span>
                                @else
                                    No file associated.
                                @endif
                            </p>
                         </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Material') }}</x-primary-button>
                             <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
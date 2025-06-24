<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Skill') }}: {{ $skill->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.skills.update', $skill->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                        {{-- Skill Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Skill Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $skill->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Slug (Optional, could be auto-generated or shown as read-only) --}}
                        <div>
                            <x-input-label for="slug" :value="__('Slug (Optional, URL-friendly)')" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $skill->slug)" placeholder="e.g., python-programming" />
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If left blank or name changes, a new slug will be auto-generated.</p>
                        </div>

                        {{-- Description --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $skill->description) }}</textarea
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Skill') }}</x-primary-button>
                             <a href="{{ route('admin.skills.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Assignment: ') }} <span class="text-blue-600">{{ $assignment->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('teacher.assignments.update', [$course, $assignment]) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Assignment Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $assignment->title)" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description / Instructions')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="6">{{ old('description', $assignment->description) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="total_points" :value="__('Total Points')" />
                            <x-text-input id="total_points" class="block mt-1 w-full" type="number" name="total_points" :value="old('total_points', $assignment->total_points)" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="due_date" :value="__('Due Date (Optional)')" />
                            <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d') : '')" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('teacher.assignments.index', $course) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Assignment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
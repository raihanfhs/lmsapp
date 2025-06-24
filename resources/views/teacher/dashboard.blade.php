<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Teacher Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome, Teacher!") }}
                     <p class="mt-4">You currently have {{ $courseCount ?? 0 }} courses.</p>
                     <p class="mt-4"><a href="{{ route('teacher.courses.index') }}" class="text-blue-500 hover:underline">Manage Your Courses</a></p>
                     {{-- Add links to other teacher functions later --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
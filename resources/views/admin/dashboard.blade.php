<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-900 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome, Admin!") }}
                    <p class="mt-4">User Statistics:</p>
                    <ul class="list-disc list-inside">
                        <li>Total Users: {{ $userCount ?? 'N/A' }}</li>
                        <li>Teachers: {{ $teacherCount ?? 'N/A' }}</li>
                        <li>Students: {{ $studentCount ?? 'N/A' }}</li>
                    </ul>
                     <p class="mt-4"><a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:underline">Manage Users</a></p>
                     {{-- Add links to other admin functions later --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
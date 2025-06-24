<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Courses') }}
        </h2>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                {{-- Judul dan Tombol Tambah Course --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium">{{ __("Your Assigned Courses") }}</h3>
                    <a href="{{-- route('teacher.courses.create') --}}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                        {{ __('Create New Course') }}
                    </a>
                </div>

                {{-- Daftar Course --}}
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                    @forelse ($courses as $course)
                        <div class="flex items-center justify-between py-4 px-2 {{ !$loop->last ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                            {{-- Informasi Course --}}
                            <div class="flex-grow">
                                <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-lg font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $course->title }}
                                </a>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <span>Code: {{ $course->code }}</span> |
                                    <span>Created: {{ $course->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                            
                            {{-- Tombol Aksi (Dengan NAMA ROUTE YANG SUDAH DIPERBAIKI) --}}
                            <div class="flex-shrink-0 ml-4">
                                {{-- INI PERBAIKANNYA --}}
                                <a href="{{ route('teacher.courses.materials.create', ['course' => $course->id]) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 mr-4">Add Material</a>
                                
                                {{-- INI PERBAIKANNYA --}}
                                <a href="{{ route('teacher.courses.edit', $course->id) }}" class="font-medium text-green-600 dark:text-green-400 hover:text-green-500 mr-4">Edit</a>
                                
                                {{-- INI PERBAIKANNYA --}}
                                <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:text-red-500" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-gray-500">{{ __("You have not been assigned to any courses yet.") }}</p>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
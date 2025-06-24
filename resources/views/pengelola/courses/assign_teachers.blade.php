<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Assign Teachers to Course') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('pengelola.courses.assign_teachers.sync', $course->id) }}" class="space-y-6">
                        @csrf       {{-- CSRF Protection --}}
                        @method('PUT') {{-- Use PUT method for updating the assignment list --}}

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Select Teachers to Assign') }}
                            </h3>
                            <div class="space-y-2">
                                {{-- Loop through all users who have the Teacher role --}}
                                @forelse ($allTeachers as $teacher)
                                    <label for="teacher_{{ $teacher->id }}" class="flex items-center">
                                        <input id="teacher_{{ $teacher->id }}"
                                               name="teacher_ids[]" {{-- Name as array to submit multiple IDs --}}
                                               type="checkbox"
                                               value="{{ $teacher->id }}" {{-- Value is the teacher's user ID --}}
                                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                               {{-- Check the box if this teacher ID is in the $assignedTeacherIds array OR in old input --}}
                                               @checked(in_array($teacher->id, old('teacher_ids', $assignedTeacherIds)))
                                               >
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $teacher->name }} ({{ $teacher->email }})</span>
                                    </label>
                                @empty
                                     <p class="text-sm text-gray-500 dark:text-gray-400">No users with the 'Teacher' role found. Please create some first.</p>
                                @endforelse
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('teacher_ids')" />
                            <x-input-error class="mt-2" :messages="$errors->get('teacher_ids.*')" /> {{-- Catch errors for individual IDs --}}
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Assignments') }}</x-primary-button>
                             {{-- Link back to course list or course edit page --}}
                             <a href="{{ route('pengelola.courses.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
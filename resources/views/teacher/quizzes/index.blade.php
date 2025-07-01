<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quizzes for: ') }} <span class="text-blue-600">{{ $course->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg bg-emerald-100 p-4 text-sm text-emerald-700" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            &larr; Back to Course
                        </a>
                        <a href="{{ route('teacher.assignments.index', $course) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ __('Manage Assignments') }}
                        </a>
                        <a href="{{ route('teacher.quizzes.create', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Create New Quiz') }}
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Title</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Duration</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Passing Grade</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($quizzes as $quiz)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">
                                        {{ $quiz->title }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700">
                                        {{ $quiz->duration }} minutes
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700">
                                        {{ $quiz->passing_grade }}%
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-2">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="rounded bg-green-500 px-4 py-2 text-xs font-medium text-white hover:bg-green-600">Manage Questions</a>
                                            <a href="{{ route('teacher.quizzes.edit', ['course' => $course, 'quiz' => $quiz]) }}" class="rounded bg-blue-500 px-4 py-2 text-xs font-medium text-white hover:bg-blue-600">Edit</a>
                                            <form method="POST" action="{{ route('teacher.quizzes.destroy', ['course' => $course, 'quiz' => $quiz]) }}" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        No quizzes have been created for this course yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $quizzes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
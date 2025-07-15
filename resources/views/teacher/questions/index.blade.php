<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Questions for Quiz: ') }} <span class="text-indigo-600">{{ $quiz->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-emerald-100 p-4 text-sm text-emerald-700" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('teacher.quizzes.index', $quiz->course) }}" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Back to Quiz List
                </a>
                <a href="{{ route('teacher.quizzes.questions.create', $quiz) }}" class="inline-flex ...">
                    {{ __('Add New Question') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="space-y-4">
                        @forelse ($quiz->questions as $index => $question)
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-start">
                                            <p class="font-semibold mr-2">{{ $index + 1 }}.</p>
                                            <div class="prose max-w-none">{!! $question->question_text !!}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <span class="font-bold">Type:</span> {{ $question->type }} | 
                                            <span class="font-bold">Points:</span> {{ $question->points }}
                                        </div>

                                        <div class="mt-3 pl-4">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach ($question->options as $option)
                                                    <li class="{{ $option->is_correct ? 'text-green-600 font-bold' : 'text-gray-700' }}">
                                                        {{ $option->option_text }}
                                                        @if ($option->is_correct)
                                                            <span class="text-green-600">(Correct Answer)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2 flex-shrink-0">
                                        <a href="{{ route('teacher.quizzes.questions.edit', ['quiz' => $quiz, 'question' => $question]) }}" class="rounded bg-blue-500 px-3 py-1 text-xs font-medium text-white hover:bg-blue-600">Edit</a>
                                        <form method="POST" action="#" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded bg-red-500 px-3 py-1 text-xs font-medium text-white hover:bg-red-600">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500">This quiz has no questions yet.</p>
                                <p class="text-gray-500">Click 'Add New Question' to begin.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
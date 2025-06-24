<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz: ') }} {{ $quizAttempt->quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <form method="POST" action="{{ route('student.quiz_attempts.submit', $quizAttempt) }}"> @csrf 
                        <div class="space-y-8">
                            @foreach ($quizAttempt->quiz->questions as $index => $question)
                                <div class="p-4 border rounded-lg">
                                    <div class="flex items-start">
                                        <div class="font-bold mr-4">{{ $index + 1 }}.</div>
                                        <div class="flex-grow">
                                            <div class="prose max-w-none">{!! $question->question_text !!}</div>
                                            <p class="text-xs text-gray-500">({{ $question->points }} Points)</p>

                                            <div class="mt-4 space-y-2">
                                                @if ($question->type === 'single_choice')
                                                    @foreach ($question->options as $option)
                                                        <label for="option_{{ $option->id }}" class="flex items-center p-2 rounded-md hover:bg-gray-100">
                                                            <input type="radio" id="option_{{ $option->id }}" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                            <span class="ml-3 text-sm text-gray-700">{{ $option->option_text }}</span>
                                                        </label>
                                                    @endforeach

                                                @elseif ($question->type === 'multiple_choice')
                                                    @foreach ($question->options as $option)
                                                        <label for="option_{{ $option->id }}" class="flex items-center p-2 rounded-md hover:bg-gray-100">
                                                            <input type="checkbox" id="option_{{ $option->id }}" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                            <span class="ml-3 text-sm text-gray-700">{{ $option->option_text }}</span>
                                                        </label>
                                                    @endforeach

                                                @elseif ($question->type === 'essay')
                                                    <textarea name="answers[{{ $question->id }}]" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>

                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <x-primary-button>
                                {{ __('Finish & Submit Answers') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
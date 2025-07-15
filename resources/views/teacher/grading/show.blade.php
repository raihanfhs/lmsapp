<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grade Essay Questions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3 class="text-2xl font-bold mb-2">Grading Quiz: {{ $attempt->quiz->title }}</h3>
                    <p class="text-gray-600 mb-4">Student: <span class="font-semibold">{{ $attempt->user->name }}</span></p>

                    <form method="POST" action="{{ route('grading.store', $attempt) }}">
                        @csrf

                        @if ($essayQuestions->isEmpty())
                            <p>There are no essay questions in this quiz to grade.</p>
                        @else
                            @foreach ($essayQuestions as $question)
                                <div class="mt-6 p-4 border rounded-lg">
                                    <div class="font-bold text-lg mb-2">
                                        Question: {{ $question->question_text }}
                                    </div>
                                    <div class="text-sm text-gray-500 mb-2">
                                        (Max Points: {{ $question->points }})
                                    </div>
                                    <div class="p-3 bg-gray-100 rounded-md mb-4">
                                        <p class="font-semibold">Student's Answer:</p>
                                        <p class="text-gray-700">{{ $studentAnswers[$question->id] ?? 'No answer provided.' }}</p>
                                    </div>

                                    <div>
                                        <x-input-label for="score_{{ $question->id }}" :value="__('Score')" />
                                        <x-text-input id="score_{{ $question->id }}" class="block mt-1 w-full" type="number" name="scores[{{ $question->id }}]" :value="old('scores.'.$question->id, 0)" required min="0" max="{{ $question->points }}" />
                                        <x-input-error :messages="$errors->get('scores.'.$question->id)" class="mt-2" />
                                    </div>

                                    <div class="mt-4">
                                        <x-input-label for="feedback_{{ $question->id }}" :value="__('Feedback (Optional)')" />
                                        <textarea id="feedback_{{ $question->id }}" name="feedback[{{ $question->id }}]" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('feedback.'.$question->id) }}</textarea>
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    {{ __('Submit Grades') }}
                                </x-primary-button>
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz Results: ') }} {{ $quizAttempt->quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 rounded-lg bg-emerald-100 p-4 text-sm text-emerald-700" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-lg font-medium text-gray-500">Your Score</h3>
                    <p class="mt-1 text-5xl font-bold text-indigo-600">{{ number_format($quizAttempt->score, 2) }}%</p>
                    @if ($quizAttempt->score >= $quizAttempt->quiz->passing_grade)
                        <p class="mt-2 font-semibold text-2xl text-green-600">PASSED</p>
                    @else
                        <p class="mt-2 font-semibold text-2xl text-red-600">FAILED</p>
                    @endif
                    <p class="text-sm text-gray-500 mt-1">Passing Grade: {{ $quizAttempt->quiz->passing_grade }}%</p>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-2xl font-bold text-gray-900">Review Your Answers</h3>
                @foreach ($quizAttempt->quiz->questions as $question)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="font-semibold">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                            <div class="mt-4 pl-4 border-l-4 
                                @php
                                    $studentAnswer = $quizAttempt->studentAnswers->where('question_id', $question->id)->pluck('option_id')->toArray();
                                    $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                                    sort($studentAnswer);
                                    sort($correctOptions);
                                    $isCorrect = ($studentAnswer == $correctOptions);
                                @endphp
                                {{ $isCorrect ? 'border-green-500' : 'border-red-500' }}">

                                <p class="text-sm font-medium mb-2">Your Answer:</p>
                                <div class="space-y-1 text-sm">
                                    @if ($question->type === 'essay')
                                        <p class="text-gray-700 p-2 bg-gray-100 rounded-md"><i>{{ $quizAttempt->studentAnswers->where('question_id', $question->id)->first()->answer_text ?? 'Not answered' }}</i></p>
                                        <p class="mt-2 text-xs text-blue-600">(Essay answers will be graded by the teacher)</p>
                                    @else
                                        @forelse ($quizAttempt->studentAnswers->where('question_id', $question->id) as $answer)
                                            <p>{{ $answer->option->option_text ?? 'Not answered' }}</p>
                                        @empty
                                            <p class="text-gray-500"><i>Not answered</i></p>
                                        @endforelse
                                    @endif
                                </div>

                                @if (!$isCorrect && $question->type !== 'essay')
                                    <p class="text-sm font-medium mt-3 mb-2 pt-3 border-t">Correct Answer:</p>
                                    <div class="space-y-1 text-sm text-green-700 font-bold">
                                        @foreach ($question->options->where('is_correct', true) as $correctOption)
                                            <p>{{ $correctOption->option_text }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
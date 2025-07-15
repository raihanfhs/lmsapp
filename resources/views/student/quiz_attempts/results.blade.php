<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Quiz Results: {{ $quizAttempt->quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary Card --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <h3 class="text-2xl font-semibold text-gray-900">Your Score: {{ $quizAttempt->score }} / {{ $quizAttempt->quiz->questions->sum('points') }}</h3>
                <p class="mt-1 text-sm text-gray-600">Submitted on: {{ $quizAttempt->end_time->format('F j, Y, g:i a') }}</p>
                <p class="mt-2 text-sm text-blue-600">Note: The final score may change after your teacher grades the essay questions.</p>
            </div>

            {{-- Loop Through Each Question --}}
            @foreach ($quizAttempt->quiz->questions as $question)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        {{-- Question Text --}}
                        <div class="flex items-start font-semibold">
                            <span class="mr-2">{{ $loop->iteration }}.</span>
                            <div class="prose max-w-none">{!! $question->question_text !!}</div>
                            <span class="ml-auto text-xs text-gray-500">({{ $question->points }} Points)</span>
                        </div>

                        {{-- Answer Section --}}
                        <div class="mt-4 pl-6">
                            @if ($question->type === 'essay')
                                @php
                                    $studentAnswer = $quizAttempt->studentAnswers->firstWhere('question_id', $question->id);
                                    $grade = $essayGrades[$question->id] ?? null;
                                @endphp
                                <div class="p-4 rounded-lg {{ $grade ? 'bg-green-50' : 'bg-yellow-50' }}">
                                    <p class="font-semibold">Your Answer:</p>
                                    <p class="text-gray-800 mb-3">
                                        {{ $studentAnswer->answer_text ?? 'You did not provide an answer.' }}
                                    </p>
                                    <hr class="my-2">
                                    @if($grade)
                                        <p><strong>Score:</strong> {{ $grade->score }}</p>
                                        @if($grade->feedback)
                                            <p class="mt-1"><strong>Teacher's Feedback:</strong> {{ $grade->feedback }}</p>
                                        @endif
                                    @else
                                        <p class="text-sm text-yellow-800">Pending teacher grading.</p>
                                    @endif
                                </div>
                            @else
                                {{-- For Multiple Choice, Single Choice, etc. --}}
                                @php
                                    $studentOptionIds = $quizAttempt->studentAnswers->where('question_id', $question->id)->pluck('option_id')->all();
                                    $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->all();
                                    $isFullyCorrect = count(array_diff($studentOptionIds, $correctOptionIds)) === 0 && count(array_diff($correctOptionIds, $studentOptionIds)) === 0;
                                @endphp
                                @foreach($question->options as $option)
                                    @php
                                        $isSelected = in_array($option->id, $studentOptionIds);
                                        $isCorrect = $option->is_correct;
                                    @endphp
                                    <div class="flex items-center my-1 p-2 rounded
                                        @if($isSelected && $isCorrect) bg-green-100 text-green-800
                                        @elseif($isSelected && !$isCorrect) bg-red-100 text-red-800
                                        @elseif(!$isSelected && $isCorrect) bg-blue-100 text-blue-800 @endif">

                                        <span class="mr-2">
                                            @if($isSelected && $isCorrect) ✔
                                            @elseif($isSelected && !$isCorrect) ✖
                                            @elseif(!$isSelected && $isCorrect) → @endif
                                        </span>
                                        <span>{{ $option->option_text }}</span>
                                        @if(!$isSelected && $isCorrect) <span class="text-xs ml-2 font-bold">(Correct Answer)</span> @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
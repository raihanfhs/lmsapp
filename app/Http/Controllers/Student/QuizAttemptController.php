<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Storage; 
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Enrollment; 
use App\Models\Certificate;
use App\Models\EssayGrading;

class QuizAttemptController extends Controller
{
    // ... (method start() dan show() Anda tetap sama, tidak perlu diubah) ...
    public function start(Request $request, Quiz $quiz): RedirectResponse
    {
        $student = Auth::user();

        // Cek jika ada percobaan yang belum selesai untuk kuis ini
        $existingAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                                    ->where('student_id', $student->id)
                                    ->whereNull('end_time') // Cari yang belum selesai
                                    ->first();

        if ($existingAttempt) {
            // Jika sudah ada, langsung arahkan ke halaman pengerjaan soal
            return redirect()->route('student.quiz_attempts.show', $existingAttempt);
        }

        // Jika tidak ada, buat percobaan baru
        $newAttempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'start_time' => now(), // Catat waktu mulai
        ]);

        return redirect()->route('student.quiz_attempts.show', $newAttempt);
    }

    public function show(QuizAttempt $quizAttempt): View
    {
        $quizAttempt->load('quiz.questions.options');
        return view('student.quiz_attempts.show', compact('quizAttempt'));
    }


    public function submit(Request $request, QuizAttempt $quizAttempt): RedirectResponse
    {
        if ($quizAttempt->end_time) {
            return redirect()->route('student.dashboard')->with('error', 'This quiz has already been submitted.');
        }

        $submittedAnswers = $request->input('answers', []);
        $totalScore = 0;
        $questions = $quizAttempt->quiz->questions()->with('options')->get();
        
        // Check if there are any essay questions in this quiz
        $hasEssayQuestions = $questions->contains('type', 'essay');

        // ----- Loop to save all answers -----
        foreach ($submittedAnswers as $questionId => $answer) {
            $question = $questions->firstWhere('id', $questionId);
            if (!$question) continue;

            $studentAnswerData = [
                'quiz_attempt_id' => $quizAttempt->id,
                'question_id' => $questionId,
            ];

            if ($question->type === 'essay') {
                if (!empty($answer)) {
                    $studentAnswerData['answer_text'] = $answer;
                    \App\Models\StudentAnswer::create($studentAnswerData);
                }
            } else {
                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $studentOptions = is_array($answer) ? array_map('intval', $answer) : [intval($answer)];
                sort($correctOptions);
                sort($studentOptions);

                if ($correctOptions == $studentOptions) {
                    $totalScore += $question->points;
                }

                foreach($studentOptions as $optionId) {
                    if ($optionId > 0) {
                        \App\Models\StudentAnswer::create(array_merge($studentAnswerData, ['option_id' => $optionId]));
                    }
                }
            }
        }

        $quizAttempt->update([
            'end_time' => now(),
            'score' => $totalScore
        ]);

        // ----- Certificate Logic -----
        // Only run this logic if there are NO essay questions.
        if (!$hasEssayQuestions) {
            if ($totalScore >= $quizAttempt->quiz->passing_score) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $course = $quizAttempt->quiz->course;
                $enrollment = \App\Models\Enrollment::where('user_id', $user->id)
                                                    ->where('course_id', $course->id)
                                                    ->first();

                if ($enrollment && $enrollment->status != 'completed') {
                    $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
                }
                
                // The rest of your existing certificate generation logic can stay here
                // ... (I have omitted it for brevity, but you should keep it)
            }
        }

        return redirect()->route('student.quiz_attempts.results', $quizAttempt)
                        ->with('success', 'Quiz submitted successfully! Essay questions will be graded by your teacher.');
    }

    // ... (method results() dan history() Anda tetap sama, tidak perlu diubah) ...
    public function results(QuizAttempt $quizAttempt): View
    {
        // Authorization check: Use 'student_id' which is the correct column name.
        if ($quizAttempt->student_id !== Auth::id()) {
            abort(403, 'This is not your quiz attempt.');
        }

        // Load all necessary data for the view
        $quizAttempt->load(['quiz.questions.options', 'studentAnswers.option']);

        // Fetch graded essays
        $essayGrades = EssayGrading::where('quiz_attempt_id', $quizAttempt->id)
                                    ->get()
                                    ->keyBy('question_id');

        return view('student.quiz_attempts.results', compact('quizAttempt', 'essayGrades'));
    }
}
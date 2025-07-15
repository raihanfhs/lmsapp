<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\EssayGrading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradingController extends Controller
{
    /**
     * Display the grading page for a specific quiz attempt.
     */
    public function showGradingPage(QuizAttempt $attempt)
    {
        // Eager load relationships to avoid N+1 issues
        $attempt->load('user', 'quiz.questions');

        // Get only the essay questions for this quiz
        $essayQuestions = $attempt->quiz->questions()->where('type', 'essay')->get();

        // Get the student's answers for those essay questions
        $studentAnswers = json_decode($attempt->answers, true);

        return view('grading.show', compact('attempt', 'essayQuestions', 'studentAnswers'));
    }

    /**
     * Store the grades for the essay questions.
     */
    public function storeGrades(Request $request, QuizAttempt $attempt)
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0', // Ensure score is not negative
            'feedback' => 'nullable|array',
            'feedback.*' => 'nullable|string',
        ]);

        foreach ($validated['scores'] as $questionId => $score) {
            // Find the original question to get the max points
            $question = Question::findOrFail($questionId);
            $maxScore = $question->points;

            // Make sure the submitted score is not higher than the max possible points
            if ($score > $maxScore) {
                return back()->withErrors(['scores.'.$questionId => 'Score cannot exceed the maximum points for this question ('.$maxScore.').'])->withInput();
            }

            // Find the student's original answer
            $studentAnswers = json_decode($attempt->answers, true);
            $studentAnswerText = $studentAnswers[$questionId] ?? 'No answer provided.';


            // Use updateOrCreate to save or update the grading record
            EssayGrading::updateOrCreate(
                [
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'user_id' => $attempt->user_id,
                ],
                [
                    'graded_by' => Auth::id(),
                    'answer' => $studentAnswerText,
                    'score' => $score,
                    'feedback' => $validated['feedback'][$questionId] ?? null,
                ]
            );
        }
        
        // After grading, update the total score for the attempt
        $this->updateTotalAttemptScore($attempt);

        return redirect()->route('teacher.dashboard')->with('success', 'Essay questions have been graded successfully!');
    }
    
    /**
     * Recalculate and update the total score for the quiz attempt.
     */
    private function updateTotalAttemptScore(QuizAttempt $attempt)
    {
        // Start with the score from multiple-choice questions
        $totalScore = $attempt->score;
        
        // Add the scores from all graded essay questions for this attempt
        $essayScores = EssayGrading::where('quiz_attempt_id', $attempt->id)->sum('score');
        
        $totalScore += $essayScores;
        
        // Update the main score on the quiz_attempts table
        $attempt->score = $totalScore;
        $attempt->save();
    }
}
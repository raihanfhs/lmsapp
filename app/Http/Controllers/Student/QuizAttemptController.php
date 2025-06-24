<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt; // Impor model QuizAttempt
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Question;
use App\Models\StudentAnswer;

class QuizAttemptController extends Controller
{
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
            // Nanti kita akan buat halaman ini
            return redirect()->route('student.quiz_attempts.show', $existingAttempt);
        }

        // Jika tidak ada, buat percobaan baru
        $newAttempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'start_time' => now(), // Catat waktu mulai
        ]);

        // Redirect ke halaman pengerjaan soal untuk percobaan yang baru dibuat
        // Nanti kita akan buat halaman ini
        return redirect()->route('student.quiz_attempts.show', $newAttempt);
    }

    public function show(QuizAttempt $quizAttempt): View
    {
        // Eager load semua relasi yang kita butuhkan untuk ditampilkan
        $quizAttempt->load('quiz.questions.options');

        // Kirim data percobaan kuis ke view
        return view('student.quiz_attempts.show', compact('quizAttempt'));
    }

    public function submit(Request $request, QuizAttempt $quizAttempt): RedirectResponse
    {
        // Pastikan student tidak bisa men-submit kuis yang sudah selesai
        if ($quizAttempt->end_time) {
            return redirect()->route('student.dashboard')->with('error', 'This quiz has already been submitted.');
        }

        $submittedAnswers = $request->input('answers', []);
        $totalScore = 0;

        // Ambil semua pertanyaan dan pilihan jawaban yang benar untuk kuis ini dalam satu query
        $questions = $quizAttempt->quiz->questions()->with('options')->get()->keyBy('id');

        foreach ($submittedAnswers as $questionId => $answer) {
            $question = $questions->get($questionId);
            if (!$question) continue; // Lanjut jika pertanyaan tidak ditemukan

            $studentAnswer = [
                'quiz_attempt_id' => $quizAttempt->id,
                'question_id' => $questionId,
            ];

            if ($question->type === 'essay') {
                $studentAnswer['answer_text'] = $answer;
            } else { // Untuk semua tipe pilihan ganda
                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();

                // Ubah jawaban menjadi array agar bisa menangani single & multiple choice
                $studentOptions = is_array($answer) ? $answer : [$answer];
                $studentOptions = array_map('intval', $studentOptions); // pastikan semua ID adalah integer

                sort($correctOptions);
                sort($studentOptions);

                if ($correctOptions == $studentOptions) {
                    $totalScore += $question->points;
                }

                // Simpan jawaban student (untuk pilihan ganda)
                // Looping untuk menyimpan setiap pilihan jika tipenya multiple_choice
                foreach($studentOptions as $optionId) {
                    StudentAnswer::create(array_merge($studentAnswer, ['option_id' => $optionId]));
                }
            }

            // Simpan jawaban esai
            if ($question->type === 'essay') {
                StudentAnswer::create($studentAnswer);
            }
        }

        // Update quiz attempt dengan waktu selesai dan total skor
        $quizAttempt->update([
            'end_time' => now(),
            'score' => $totalScore
        ]);
        // Redirect ke halaman hasil atau dashboard dengan pesan sukses
        return redirect()->route('student.quiz_attempts.results', $quizAttempt)
                        ->with('success', 'Quiz submitted successfully!');
    }

    public function results(QuizAttempt $quizAttempt): View
    {
        // Pastikan student hanya bisa melihat hasilnya sendiri
        if ($quizAttempt->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load semua data yang dibutuhkan dalam satu query
        $quizAttempt->load([
            'quiz.questions.options', // Memuat kuis, pertanyaan, dan semua pilihan jawaban
            'studentAnswers.question.options' // Memuat jawaban student, dan detail pertanyaan & pilihan jawabannya
        ]);

        return view('student.quiz_attempts.results', compact('quizAttempt'));
    }

    public function history(): View
    {
        $student = Auth::user();

        // Ambil semua percobaan kuis milik student ini, urutkan dari yang terbaru
        // Eager load relasi 'quiz' agar tidak ada N+1 query di view
        $attempts = $student->quizAttempts()->with('quiz')->latest()->paginate(15);

        return view('student.quiz_attempts.history', compact('attempts'));
    }
}
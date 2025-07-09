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
// --- Mulai Penambahan untuk Sertifikat ---
use Illuminate\Support\Facades\Storage; 
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Enrollment; 
use App\Models\Certificate;
// --- Akhir Penambahan ---

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
        $questions = $quizAttempt->quiz->questions()->with('options')->get()->keyBy('id');

        foreach ($submittedAnswers as $questionId => $answer) {
            $question = $questions->get($questionId);
            if (!$question) continue;

            $studentAnswer = [
                'quiz_attempt_id' => $quizAttempt->id,
                'question_id' => $questionId,
            ];

            if ($question->type === 'essay') {
                $studentAnswer['answer_text'] = $answer;
                StudentAnswer::create($studentAnswer); // Simpan jawaban esai
            } else {
                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $studentOptions = is_array($answer) ? array_map('intval', $answer) : [intval($answer)];

                sort($correctOptions);
                sort($studentOptions);

                if ($correctOptions == $studentOptions) {
                    $totalScore += $question->points;
                }

                foreach($studentOptions as $optionId) {
                    StudentAnswer::create(array_merge($studentAnswer, ['option_id' => $optionId]));
                }
            }
        }

        $quizAttempt->update([
            'end_time' => now(),
            'score' => $totalScore
        ]);
        
        // ==========================================================
        // ===== MULAI LOGIKA SERTIFIKAT DI DALAM BLOK IF INI =====
        // ==========================================================
        
        // Cek apakah skor siswa memenuhi atau melebihi skor kelulusan
        if ($totalScore >= $quizAttempt->quiz->passing_score) {
            
            $user = Auth::user();
            $course = $quizAttempt->quiz->course; // Ambil kursus dari kuis

            // Dapatkan record pendaftaran (enrollment) untuk kursus ini
            $enrollment = Enrollment::where('user_id', $user->id)
                                    ->where('course_id', $course->id)
                                    ->first();

            // Tandai kursus sebagai selesai & jalankan logika sertifikat
            if ($enrollment) {
                // Pastikan hanya dijalankan sekali dengan mengecek status
                if ($enrollment->status != 'completed') {
                    $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
                }

                // Cek jika kursus punya template sertifikat dan sertifikat belum ada
                $certificateExists = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->exists();
                
                if ($course->certificate_template_id && !$certificateExists) {
                    $template = $course->certificateTemplate; // Relasi dari Model Course
                    
                    $data = [
                        'nama_peserta' => $user->name,
                        'nama_kursus'  => $course->title,
                        'tanggal_selesai' => now()->translatedFormat('d F Y'),
                    ];
                    
                    $content = str_replace(
                        ['{nama_peserta}', '{nama_kursus}', '{tanggal_selesai}'],
                        [$data['nama_peserta'], $data['nama_kursus'], $data['tanggal_selesai']],
                        $template->content
                    );
                    
                    $pdfData = [
                        'content' => $content, 
                        'background_url' => Storage::disk('public')->url($template->background_image_path)
                    ];
                    
                    $pdf = Pdf::loadView('certificates.template', $pdfData)->setPaper('a4', 'landscape');
                    
                    $filename = 'sertifikat-' . $user->id . '-' . $course->id . '.pdf';
                    $path = 'certificates/' . $filename;
                    Storage::disk('public')->put($path, $pdf->output());
                    
                    Certificate::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'issue_date' => now(),
                        'file_path' => $path,
                    ]);
                }
            }
        }
        // ==========================================================
        // ===== AKHIR DARI LOGIKA SERTIFIKAT =====================
        // ==========================================================
        
        return redirect()->route('student.quiz_attempts.results', $quizAttempt)
                         ->with('success', 'Quiz submitted successfully!');
    }

    // ... (method results() dan history() Anda tetap sama, tidak perlu diubah) ...
    public function results(QuizAttempt $quizAttempt): View
    {
        if ($quizAttempt->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $quizAttempt->load(['quiz.questions.options', 'studentAnswers.question.options']);
        return view('student.quiz_attempts.results', compact('quizAttempt'));
    }

    public function history(): View
    {
        $student = Auth::user();
        $attempts = $student->quizAttempts()->with('quiz')->latest()->paginate(15);
        return view('student.quiz_attempts.history', compact('attempts'));
    }
}
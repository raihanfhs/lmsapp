<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course; // Impor model Course
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreQuizRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateQuizRequest;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        // Ambil semua kuis yang berhubungan dengan kursus ini
        $quizzes = $course->quizzes()->latest()->paginate(10);

        // Kirim data ke view (view ini akan kita buat di langkah selanjutnya)
        return view('teacher.quizzes.index', compact('course', 'quizzes'));
    }

    // ... method lainnya (create, store, dll) akan kita isi nanti


    public function create(Course $course): View // <-- Modifikasi di sini
    {
        // Kita hanya perlu mengirimkan data $course ke view
        // agar form tahu kuis ini akan dibuat untuk kursus yang mana.
        return view('teacher.quizzes.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizRequest $request, Course $course): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['course_id'] = $course->id;
        $quiz = \App\Models\Quiz::create($validatedData);

        return redirect()->route('teacher.quizzes.questions.index', $quiz)
                    ->with('success', 'Quiz created successfully! Now you can add questions to it.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Quiz $quiz): View
    {
        return view('teacher.quizzes.edit', compact('course', 'quiz'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course, Quiz $quiz): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('quizzes')->where('course_id', $course->id)->ignore($quiz->id)],
            'description'   => 'nullable|string',
            'duration'      => 'required|integer|min:1',
            'pass_grade'    => 'required|integer|min:0|max:100', 
            'max_attempts'  => 'nullable|integer|min:1',
        ]);

        $quiz->update($validatedData);

        return redirect()->route('teacher.quizzes.index', $course)
                        ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function viewAttempts(Quiz $quiz)
    {
        // Eager load the user for each attempt to show their name
        $attempts = $quiz->attempts()->with('user')->latest()->paginate(20);

        return view('teacher.quizzes.attempts', compact('quiz', 'attempts'));
    }
}

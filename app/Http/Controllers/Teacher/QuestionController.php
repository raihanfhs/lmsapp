<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz; // Impor model Quiz
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateQuestionRequest;

class QuestionController extends Controller
{
    public function index(Quiz $quiz): View
    {
        // Eager load relasi 'options' untuk setiap pertanyaan agar lebih efisien
        $quiz->load('questions.options');

        // Kirim data quiz (yang sudah berisi pertanyaan dan opsinya) ke view
        return view('teacher.questions.index', compact('quiz'));
    }

    public function create(Quiz $quiz): View
    {
        // Kirim data kuis ke view agar kita tahu pertanyaan ini milik kuis mana
        return view('teacher.questions.create', compact('quiz'));
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz): RedirectResponse
    {
        // Ambil data yang sudah divalidasi
        $validatedData = $request->validated();

        // Tambahkan quiz_id secara manual untuk menghubungkan pertanyaan ini ke kuis yang benar
        $validatedData['quiz_id'] = $quiz->id;

        // Buat record pertanyaan baru di database
        $question = Question::create($validatedData);

        // Redirect kembali ke halaman daftar pertanyaan dengan pesan sukses
        return redirect()->route('teacher.quizzes.questions.index', $quiz)
                         ->with('success', 'Question created successfully. Now add the options.');
    }

    public function edit(Quiz $quiz, Question $question): View
    {
        // Kirim data kuis dan pertanyaan yang spesifik ke view
        return view('teacher.questions.edit', compact('quiz', 'question'));
    }

        public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question): RedirectResponse
    {
        // Update detail pertanyaan dengan data yang sudah divalidasi
        $question->update($request->validated());

        // Redirect kembali ke halaman daftar pertanyaan dengan pesan sukses
        return redirect()->route('teacher.quizzes.questions.index', $quiz)
                         ->with('success', 'Question updated successfully.');
    }
}
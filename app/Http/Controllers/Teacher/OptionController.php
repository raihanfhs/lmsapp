<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class OptionController extends Controller
{
    public function store(Request $request, Question $question): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'option_text' => 'required|string|max:255',
            'is_correct' => 'nullable|string', // Checkbox mengirim '1' atau tidak sama sekali
        ]);

        // Cek apakah checkbox 'is_correct' dicentang atau tidak.
        // Ini adalah cara yang benar untuk menangani checkbox.
        $isCorrect = $request->has('is_correct');

        // Logika khusus untuk soal single_choice:
        // Jika jawaban baru ditandai benar, maka nonaktifkan jawaban benar yang lain.
        if ($question->type == 'single_choice' && $isCorrect) {
            $question->options()->update(['is_correct' => false]);
        }

        // Buat record option baru dengan logika yang sudah benar
        $question->options()->create([
            'option_text' => $request->option_text,
            'is_correct' => $isCorrect, // Simpan nilai true/false
        ]);

        // Redirect kembali ke halaman edit pertanyaan
        return redirect()->route('teacher.quizzes.questions.edit', ['quiz' => $question->quiz_id, 'question' => $question])
                        ->with('success', 'Option added successfully.');
    }

    public function destroy(Option $option): RedirectResponse
    {
        // Sebelum menghapus, kita simpan dulu data pertanyaan induknya
        // agar tahu harus redirect kembali ke mana.
        $question = $option->question;

        // Hapus option dari database
        $option->delete();

        // Redirect kembali ke halaman edit pertanyaan
        return redirect()->route('teacher.quizzes.questions.edit', ['quiz' => $question->quiz_id, 'question' => $question])
                        ->with('success', 'Option deleted successfully.');
}
}
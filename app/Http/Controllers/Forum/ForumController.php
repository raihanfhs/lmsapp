<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Menampilkan daftar semua thread diskusi dalam sebuah course.
     */
    public function index(Course $course): View
    {
        // Validasi: Pastikan user terdaftar di kursus ini untuk bisa melihat forum
        // (Anda bisa tambahkan pengecekan ini jika forum bersifat private)

        // Eager load untuk optimasi query (menghindari N+1 problem)
        $threads = $course->forumThreads()
                          ->with('user', 'posts') // Ambil data pembuat thread & post-nya
                          ->latest()
                          ->paginate(10);

        return view('forum.index', compact('course', 'threads'));
    }

    public function create(Course $course): View
    {
        return view('forum.create', compact('course'));
    }

    /**
     * Menyimpan thread diskusi baru ke database.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'body' => 'required|string|min:10',
        ]);

        try {
            // Kita gunakan DB Transaction untuk memastikan kedua data (thread dan post) berhasil dibuat.
            // Jika salah satu gagal, semua akan dibatalkan.
            DB::beginTransaction();

            // 1. Buat thread utama
            $thread = ForumThread::create([
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'title' => $validated['title'],
            ]);

            // 2. Buat post pertama untuk thread tersebut
            $thread->posts()->create([
                'user_id' => auth()->id(),
                'body' => $validated['body'],
            ]);

            DB::commit();

            // Arahkan ke halaman detail thread yang baru dibuat
            // (Route 'forum.show' akan kita buat di langkah selanjutnya)
            return redirect()->route('forum.show', ['course' => $course->id, 'thread' => $thread->id])
                             ->with('success', 'Diskusi berhasil dimulai!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error jika perlu: Log::error($e->getMessage());
            return back()->with('error', 'Gagal memulai diskusi. Silakan coba lagi.');
        }
    }

    public function show(Course $course, ForumThread $thread): View
    {
        // Eager load posts dan user yang membuat post
        $thread->load(['posts.user']);

        return view('forum.show', compact('course', 'thread'));
    }
}
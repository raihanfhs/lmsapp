<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Menyimpan balasan baru ke dalam sebuah thread.
     */
    public function store(Request $request, ForumThread $thread): RedirectResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|min:5',
        ]);

        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        // Redirect kembali ke halaman thread yang sama dengan pesan sukses
        return redirect()->route('forum.show', ['course' => $thread->course_id, 'thread' => $thread->id])
                         ->with('success', 'Balasan berhasil dikirim!');
    }
}
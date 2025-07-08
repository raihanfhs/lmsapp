<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        // Ambil semua notifikasi milik user, urutkan dari yang terbaru, dan gunakan paginasi
        $notifications = Auth::user()
                             ->notifications()
                             ->latest() // Mengurutkan dari yang paling baru
                             ->paginate(15); // Tampilkan 15 notifikasi per halaman

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = DatabaseNotification::findOrFail($id);

        // Pastikan user hanya bisa mengakses notifikasinya sendiri
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        // Tandai sudah dibaca
        $notification->markAsRead();

        // Redirect ke halaman yang relevan
        $courseId = $notification->data['course_id'] ?? null;
        if ($courseId) {
            // Saat ini, kita arahkan ke halaman detail kursus
            return redirect()->route('student.courses.show', $courseId);
        }

        // Fallback jika tidak ada course_id
        return redirect()->back();
    }
    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('status', 'All notifications marked as read.');
    }
}
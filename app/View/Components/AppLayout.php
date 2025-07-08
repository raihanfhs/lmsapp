<?php
namespace App\View\Components;

use Illuminate\Support\Facades\Auth; // <-- Tambahkan ini
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $user = Auth::user();
        $unreadNotifications = collect(); // Default ke koleksi kosong

        if ($user) {
            $unreadNotifications = $user->unreadNotifications()->take(5)->get();
        }

        // Kirim notifikasi ke view 'layouts.app'
        return view('layouts.app', [
            'unreadNotifications' => $unreadNotifications
        ]);
    }
}
<?php
// app/Http/Controllers/TrixController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrixController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi request, pastikan yang diupload adalah file gambar
        $request->validate([
            'file' => 'required|image|max:2048', // Maksimal 2MB, hanya format gambar
        ]);

        // 2. Simpan file gambar ke dalam storage
        // File akan disimpan di storage/app/public/trix-attachments
        $path = $request->file('file')->store('trix-attachments', 'public');

        // 3. Buat URL yang bisa diakses publik untuk gambar tersebut
        $url = asset('storage/' . $path);

        // 4. Kembalikan URL dalam format JSON yang diharapkan oleh Trix
        return response()->json([
            'url' => $url,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        $templates = CertificateTemplate::latest()->paginate(10);
        return view('pengelola.certificate_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('pengelola.certificate_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'background_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'content' => 'required|string',
        ]);

        $path = $request->file('background_image')->store('certificate_backgrounds', 'public');

        CertificateTemplate::create([
            'name' => $request->name,
            'background_image_path' => $path,
            'content' => $request->content,
        ]);

        return redirect()->route('pengelola.certificate-templates.index')
                         ->with('success', 'Template sertifikat berhasil dibuat.');
    }

    public function edit(CertificateTemplate $certificateTemplate)
    {
        return view('pengelola.certificate_templates.edit', compact('certificateTemplate'));
    }

    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'content' => 'required|string',
        ]);

        $path = $certificateTemplate->background_image_path;
        if ($request->hasFile('background_image')) {
            // Hapus gambar lama
            Storage::disk('public')->delete($path);
            // Simpan gambar baru
            $path = $request->file('background_image')->store('certificate_backgrounds', 'public');
        }

        $certificateTemplate->update([
            'name' => $request->name,
            'background_image_path' => $path,
            'content' => $request->content,
        ]);

        return redirect()->route('pengelola.certificate-templates.index')
                         ->with('success', 'Template sertifikat berhasil diperbarui.');
    }

    public function destroy(CertificateTemplate $certificateTemplate)
    {
        // Hapus gambar dari storage
        Storage::disk('public')->delete($certificateTemplate->background_image_path);
        
        // Hapus record dari database
        $certificateTemplate->delete();

        return redirect()->route('pengelola.certificate-templates.index')
                         ->with('success', 'Template sertifikat berhasil dihapus.');
    }
}
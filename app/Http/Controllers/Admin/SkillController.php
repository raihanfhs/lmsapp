<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; // Base Controller
use App\Models\Skill;             // Skill model
use Illuminate\Http\Request;       // For other methods later
use Illuminate\View\View;          // For returning views
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
/**
 * Display a listing of the resource.
 */
    public function index(): View
    {
        // Fetch all skills, order by name, and paginate
        $skills = Skill::orderBy('name', 'asc')->paginate(15);
        // In Admin\SkillController.php, index() method:
        $skills = Skill::withCount('users')->orderBy('name', 'asc')->paginate(15);

        // Return the admin view for listing skills
        // View file: resources/views/admin/skills/index.blade.php (we create next)
        return view('admin.skills.index', compact('skills'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Return the view file located at resources/views/admin/skills/create.blade.php
        return view('admin.skills.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse // Use StoreSkillRequest later
    {
        // TODO: Move validation to StoreSkillRequest later
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name',
            'slug' => 'nullable|string|max:255|unique:skills,slug', // Slug should also be unique if provided
            'description' => 'nullable|string',
        ]);
    
        // Auto-generate slug if not provided or if the name changed and slug was based on old name
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Ensure auto-generated slug is unique too (could add a loop with -1, -2 if needed)
            $count = Skill::where('slug', $validated['slug'])->count();
            if ($count > 0) {
                // A simple way to handle potential duplicate slugs from auto-generation
                // For a more robust solution, you might loop and append -2, -3, etc.
                // or use a package that handles unique slug generation.
                // For now, let's append a timestamp for uniqueness if a direct slug isn't unique.
                // This is less ideal than a dedicated unique slug generator.
                 $originalSlug = $validated['slug'];
                 $i = 1;
                 while(Skill::where('slug', $validated['slug'])->exists()) {
                     $validated['slug'] = $originalSlug . '-' . $i++;
                 }
            }
        } else {
             // If slug is provided, ensure it's a valid slug format
             $validated['slug'] = Str::slug($validated['slug']);
        }
    
    
        Skill::create($validated);
    
        return redirect()->route('admin.skills.index')
                         ->with('success', 'Skill created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill): View
    {
        // The $skill variable is automatically injected by Laravel.
        return view('admin.skills.edit', compact('skill'));
    }

    /**
     * Update the specified resource in storage.
     */
/**
 * Update the specified resource in storage.
 */
    public function update(Request $request, Skill $skill): RedirectResponse // Use UpdateSkillRequest later
    {
        // TODO: Move validation to UpdateSkillRequest later
        $validated = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('skills')->ignore($skill->id)],
            'slug' => ['nullable','string','max:255', Rule::unique('skills')->ignore($skill->id)],
            'description' => 'nullable|string',
        ]);

        // Auto-generate or update slug if name changed or slug is empty/changed
        if (empty($validated['slug']) || $request->name !== $skill->name) {
            $validated['slug'] = Str::slug($validated['name']);
            // Basic unique check for auto-generated slug, append number if duplicate
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Skill::where('slug', $validated['slug'])->where('id', '!=', $skill->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        } else {
            // If slug is provided, ensure it's a valid slug format
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $skill->update($validated);

        return redirect()->route('admin.skills.index')
                        ->with('success', 'Skill updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill): RedirectResponse
    {
        $skillName = $skill->name; // Get name for the message
        $skill->delete();
    
        return redirect()->route('admin.skills.index')
                         ->with('success', "Skill '{$skillName}' deleted successfully. It has been removed from all teachers who had it.");
    }
}

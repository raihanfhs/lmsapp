<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Base Controller
use App\Models\Division;             // Division model
use Illuminate\Http\Request;       // For other methods later
use Illuminate\View\View;          // For returning views
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;  // For redirects later

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Fetch all divisions, order by name, and paginate
        $divisions = Division::orderBy('name', 'asc')->paginate(15);
        $divisions = Division::withCount('users')->orderBy('name', 'asc')->paginate(15);
    
        // Return the admin view for listing divisions
        // View file: resources/views/admin/divisions/index.blade.php (we create next)
        return view('admin.divisions.index', compact('divisions'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Return the view file located at resources/views/admin/divisions/create.blade.php
        return view('admin.divisions.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse // Use StoreDivisionRequest later
    {
        // TODO: Move validation to StoreDivisionRequest later
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
            'description' => 'nullable|string',
            // 'slug' => 'nullable|string|max:255|unique:divisions,slug', // If you add slugs
        ]);
    
        // If auto-generating slug from name:
        // $validated['slug'] = Str::slug($validated['name']);
    
        // Create the division using the validated data
        Division::create($validated);
    
        // Redirect back to the division list with a success message
        return redirect()->route('admin.divisions.index')
                         ->with('success', 'Division created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(Division $division): View
    {
        // Eager load users for this division
        $division->load('users'); // Assumes 'users' relationship exists on Division model
        return view('admin.divisions.show', compact('division'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division): View
    {
        // The $division variable is automatically injected by Laravel
        // containing the division matching the ID from the URL.
    
        return view('admin.divisions.edit', compact('division'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division): RedirectResponse // Use UpdateDivisionRequest later
    {
        // TODO: Move validation to UpdateDivisionRequest later
        $validated = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('divisions')->ignore($division->id)],
            'description' => 'nullable|string',
        ]);
    
        // Update the division record
        $division->update($validated);
    
        // Redirect back to the division list with a success message
        return redirect()->route('admin.divisions.index')
                         ->with('success', 'Division updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division): RedirectResponse
    {
    
        $divisionName = $division->name; // Get name for the message
        $division->delete();
    
        return redirect()->route('admin.divisions.index')
                         ->with('success', "Division '{$divisionName}' deleted successfully. Users in this division now have no division assigned.");
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the assignments for a course.
     */
    public function index(Course $course): View
    {
        $assignments = $course->assignments()->latest()->get();
        return view('teacher.assignments.index', compact('course', 'assignments'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(Course $course): View
    {
        return view('teacher.assignments.create', compact('course'));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'total_points' => 'required|integer|min:1',
        ]);

        $course->assignments()->create($validated);

        return redirect()->route('teacher.assignments.index', $course)->with('success', 'Assignment created successfully.');
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(Course $course, Assignment $assignment): View
    {
        return view('teacher.assignments.edit', compact('course', 'assignment'));
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, Course $course, Assignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'total_points' => 'required|integer|min:1',
        ]);

        $assignment->update($validated);

        return redirect()->route('teacher.assignments.index', $course)->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(Course $course, Assignment $assignment): RedirectResponse
    {
        $assignment->delete();
        return redirect()->route('teacher.assignments.index', $course)->with('success', 'Assignment deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseSectionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        // Optional: Add authorization to ensure the user is a teacher of this course
        // Gate::authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Determine the order for the new section
        $lastOrder = $course->sections()->max('order');

        CourseSection::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'order' => $lastOrder + 1,
        ]);

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Section created successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, CourseSection $section): RedirectResponse
    {
        // Optional: Add authorization
        // Gate::authorize('update', $course);

        // Note: Materials within the section will be deleted automatically
        // because of the onDelete('cascade') in the migration.
        $section->delete();

        return redirect()->route('teacher.courses.show', 'course')->with('success', 'Section deleted successfully.');
    }
}
<?php
// File: app/Http/Controllers/Teacher/MeetingController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Meeting; // <-- GANTI DARI OnlineMeeting
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class MeetingController extends Controller
{
    use AuthorizesRequests; // Pastikan ini ada

    public function create(Course $course)
    {
        // Panggil Policy: Apakah user ini boleh 'meng-update' course ini?
        // Izin untuk membuat meeting dianggap sama dengan izin untuk update course.
        $this->authorize('update', $course);

        return view('teacher.meetings.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        // ... sisa kode validasi dan store ...
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date',
            'type' => 'required|in:online,offline',
            'meeting_link' => 'required_if:type,online|nullable|url',
            'location' => 'required_if:type,offline|nullable|string|max:255',
        ]);

        $course->meetings()->create($request->all());

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Meeting scheduled successfully.');
    }

    public function edit(Course $course, Meeting $meeting)
    {
        $this->authorize('update', $course);
        return view('teacher.meetings.edit', compact('course', 'meeting'));
    }

    public function update(Request $request, Course $course, Meeting $meeting)
    {
        $this->authorize('update', $course);

        // ... sisa kode validasi dan update ...
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_datetime' => 'required|date',
            'type' => 'required|in:online,offline',
            'meeting_link' => 'required_if:type,online|nullable|url',
            'location' => 'required_if:type,offline|nullable|string|max:255',
        ]);

        $meeting->update($validated);

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Course $course, Meeting $meeting)
    {
        $this->authorize('update', $course);
        $meeting->delete();
        return redirect()->route('teacher.courses.show', $course)->with('success', 'Meeting deleted successfully.');
    }
}
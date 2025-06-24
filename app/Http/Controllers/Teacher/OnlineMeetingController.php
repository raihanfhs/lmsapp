<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\OnlineMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule; // For validation if needed

class OnlineMeetingController extends Controller
{
    // Controller methods will go here
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new online meeting for a specific course.
     *
     * @param Course $course Automatically injected by route-model binding
     * @return View|RedirectResponse
     */
    public function create(Course $course): View|RedirectResponse
    {
        // Authorization Check: Ensure the logged-in teacher is assigned to this course
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }

        // Return the view for the create form, passing the course
        // View file: resources/views/teacher/meetings/create.blade.php (we create this next)
        return view('teacher.meetings.create', compact('course'));
    }
    /**
     * Store a newly created online meeting in storage for a specific course.
     *
     * @param Request $request // Use a specific StoreOnlineMeetingRequest later
     * @param Course $course Automatically injected course from the route
     * @return RedirectResponse
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        // Authorization Check: Ensure the logged-in teacher is assigned to this course
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }

        // TODO: Move validation to a StoreOnlineMeetingRequest later
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date|after_or_equal:now', // Meeting must be in the future or now
            'meeting_link' => 'required|url|max:1000', // Validate as URL
            'description' => 'nullable|string',
        ]);

        // Prepare data for database record, including the teacher_id
        $data = [
            'course_id' => $course->id,
            'teacher_id' => Auth::id(), // Assign the logged-in teacher as the creator/host
            'title' => $validated['title'],
            'meeting_datetime' => $validated['meeting_datetime'],
            'meeting_link' => $validated['meeting_link'],
            'description' => $validated['description'] ?? null,
        ];

        // Create the OnlineMeeting record
        OnlineMeeting::create($data);

        // Redirect after successful creation
        // We will redirect to the teacher's course show page, where meetings will be listed
        return redirect()->route('teacher.courses.show', $course->id)
                        ->with('success', 'Online meeting "' . $validated['title'] . '" scheduled successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(OnlineMeeting $onlineMeeting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Show the form for editing the specified online meeting.
     *
     * @param Course $course The course the meeting belongs to.
     * @param OnlineMeeting $meeting The specific meeting to edit.
     * @return View|RedirectResponse
     */
    public function edit(Course $course, OnlineMeeting $meeting): View|RedirectResponse
    {
        // Authorization Check 1: Ensure teacher is assigned to this course.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }
        // Authorization Check 2: Ensure the meeting belongs to this course AND this teacher scheduled it.
        // (Or any assigned teacher can edit - for now, let's allow any assigned teacher of the course to edit any meeting of that course)
        if ($meeting->course_id !== $course->id) {
            abort(403, 'Meeting does not belong to this course.');
        }
        // Optional: If only the creator teacher can edit:
        // if ($meeting->teacher_id !== Auth::id()) {
        //     abort(403, 'You did not schedule this meeting.');
        // }

        // Return the edit view
        // View file: resources/views/teacher/meetings/edit.blade.php (we create next)
        return view('teacher.meetings.edit', compact('course', 'meeting'));
    }
    /**
     * Update the specified online meeting in storage.
     *
     * @param Request $request // Use UpdateOnlineMeetingRequest later
     * @param Course $course
     * @param OnlineMeeting $meeting
     * @return RedirectResponse
     */
    public function update(Request $request, Course $course, OnlineMeeting $meeting): RedirectResponse
    {
        // Authorization Check 1: Ensure teacher is assigned to this course.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
             abort(403, 'You are not assigned to teach this course.');
        }
        // Authorization Check 2: Ensure the meeting belongs to this course
        if ($meeting->course_id !== $course->id) {
             abort(403, 'Meeting does not belong to this course.');
        }
        // Optional: If only the creator teacher can edit:
        // if ($meeting->teacher_id !== Auth::id()) {
        //     abort(403, 'You did not schedule this meeting.');
        // }

        // TODO: Move validation to UpdateOnlineMeetingRequest later
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date|after_or_equal:now',
            'meeting_link' => 'required|url|max:1000',
            'description' => 'nullable|string',
        ]);

        // Update the meeting record. teacher_id is not updated here as it's the scheduler.
        $meeting->update($validated);

        return redirect()->route('teacher.courses.show', $course->id)
                         ->with('success', 'Online meeting updated successfully.');
    }

    /**
     * Remove the specified online meeting from storage.
     *
     * @param Course $course
     * @param OnlineMeeting $meeting
     * @return RedirectResponse
     */
    public function destroy(Course $course, OnlineMeeting $meeting): RedirectResponse
    {
        // Authorization Check 1: Ensure teacher is assigned to this course.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }
        // Authorization Check 2: Ensure the meeting belongs to this course
        if ($meeting->course_id !== $course->id) {
            abort(403, 'Meeting does not belong to this course.');
        }
        // Optional: If only the creator teacher can delete:
        // if ($meeting->teacher_id !== Auth::id()) {
        //     abort(403, 'You did not schedule this meeting.');
        // }

        // Get the meeting title for the success message before deleting
        $meetingTitle = $meeting->title;

        // Delete the meeting record from the database
        $meeting->delete();

        // Redirect back to the course detail page with a success message
        return redirect()->route('teacher.courses.show', $course->id)
                        ->with('success', 'Online meeting "' . $meetingTitle . '" deleted successfully.');
    }
}

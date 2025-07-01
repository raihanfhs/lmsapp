<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class AssignmentSubmissionController extends Controller
{
    /**
     * Show the form for creating a new assignment submission.
     */
    public function create(Assignment $assignment): View|RedirectResponse
    {
        $student = Auth::user();

        // Authorization: Check if the student has already submitted for this assignment
        $existingSubmission = $assignment->submissions()->where('student_id', $student->id)->first();

        if ($existingSubmission) {
            // If they have, redirect them back to the course page with an error message
            return redirect()->route('student.courses.show', $assignment->course_id)
                             ->with('error', 'You have already submitted your work for this assignment.');
        }

        // If not, show the submission form
        return view('student.assignments.submit', compact('assignment'));
    }

    /**
     * Store a newly created assignment submission in storage.
     */
    public function store(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = Auth::user();

        // Authorization: Double-check to prevent duplicate submissions
        $existingSubmission = $assignment->submissions()->where('student_id', $student->id)->first();
        if ($existingSubmission) {
            return redirect()->route('student.courses.show', $assignment->course_id)
                             ->with('error', 'You have already submitted your work for this assignment.');
        }

        // Validation: Ensure a file is uploaded and it's a valid type
        $request->validate([
            'submission_file' => 'required|file|mimes:pdf,doc,docx,zip,jpg,png|max:10240', // Max 10MB
            'student_comments' => 'nullable|string|max:1000',
        ]);

        // File Handling: Store the uploaded file
        $filePath = $request->file('submission_file')->store(
            'assignment_submissions/' . $assignment->id, 'public'
        );

        // Database: Create the submission record
        $assignment->submissions()->create([
            'student_id' => $student->id,
            'file_path' => $filePath,
            'student_comments' => $request->input('student_comments'),
            'submitted_at' => Carbon::now(),
        ]);

        // Redirect back to the course page with a success message
        return redirect()->route('student.courses.show', $assignment->course_id)
                         ->with('success', 'Your assignment was submitted successfully!');
    }
}
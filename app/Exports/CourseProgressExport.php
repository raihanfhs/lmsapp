<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CourseProgressExport implements FromCollection, WithHeadings, WithMapping
{
    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // This is the same logic from our progress controller method
        $this->course->load('enrollments.student', 'grades');

        return $this->course->enrollments;
    }

    /**
     * @var Enrollment $enrollment
     */
    public function map($enrollment): array
    {
        $studentGrade = $this->course->grades->firstWhere('student_id', $enrollment->student_id);

        return [
            $enrollment->student->name,
            $enrollment->student->email,
            $enrollment->created_at->format('d M Y'),
            $studentGrade ? $studentGrade->grade : 'Not Graded',
            $studentGrade ? ($studentGrade->is_passed ? 'Passed' : 'Failed') : 'In Progress',
        ];
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Email',
            'Enrolled Date',
            'Grade',
            'Status',
        ];
    }
}
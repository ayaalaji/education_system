<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Log;

class CourseReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $courseId;

    public function __construct($courseId)
    {
        $this->courseId = $courseId;
    }

    public function collection()
    {
        // Load the course with users and their tasks
        $course = Course::with(['users.tasks' => function ($query) {
            $query->whereNotNull('grade'); // Only include tasks with grades
        }])->find($this->courseId);

        // If no course is found, return an empty collection
        if (!$course) {
            return collect([]);
        }

        // Map over each user to calculate submitted tasks count and average grade
        return $course->users->map(function ($user) {
            Log::info($user);
            $tasksWithGrades = $user->tasks->filter(function ($task) use ($user) {
                return $task->pivot->grade !== null && $task->pivot->student_id === $user->id;
            });
            $submittedTasksCount = $tasksWithGrades->count();
            $totalGrade = $tasksWithGrades->sum(fn($task) => $task->pivot->grade);

            // Calculate average grade
            $averageGrade = $submittedTasksCount > 0 ? $totalGrade / $submittedTasksCount : 0;

            // Return the data for this user
            return [
                'student_id' => $user->id,
                'student_name' => $user->name,
                'submitted_tasks_count' => $submittedTasksCount,
                'average_grade' => $averageGrade,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Student Name',
            'Submitted Tasks Count',
            'Average Grade',
        ];
    }
}

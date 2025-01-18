<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;

class CourseReportExport implements FromCollection, WithHeadings, ShouldAutoSize , WithStyles
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
    /**
     * Formate the row in Excel
     */
    public function styles($sheet)
    {
        // Format the headers (first row)
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // Set text color to white
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'], // Set background color to green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Center horizontally
                'vertical' => Alignment::VERTICAL_CENTER, // Center vertically
            ],
        ]);

        // Apply borders to all cells
        $sheet->getStyle('A1:D' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Thin border style
                    'color' => ['rgb' => '000000'], // Set border color to black
                ],
            ],
        ]);

        // Automatically adjust column width based on the content
        foreach (range('A', 'D') as $column) {
            $maxLength = 0;

            // Iterate through all rows to calculate the maximum content length in each column
            foreach ($sheet->getRowIterator() as $row) {
                $cellValue = $sheet->getCell($column . $row->getRowIndex())->getValue();
                if ($cellValue !== null) {
                    $maxLength = max($maxLength, strlen((string) $cellValue));
                }
            }

            // Set the column width with some padding
            $sheet->getColumnDimension($column)->setWidth($maxLength + 2); // Add padding
        }

        return [];
    }
}

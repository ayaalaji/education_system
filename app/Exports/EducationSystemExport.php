<?php
namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EducationSystemExport implements FromArray, WithHeadings, WithEvents
{
    private $data = [];
    private $totalCategories = 0;
    private $totalCourses = 0;
    private $totalTeachers = 0;
    private $totalStudents = 0;

    public function __construct()
    {
        $categories = Category::with(['courses.teacher', 'courses.users'])->get();

        foreach ($categories as $category) {
            foreach ($category->courses as $course) {
                $this->data[] = [
                    'course_name' => $course->title,
                    'category_name' => $category->name,
                    'teacher_name' => $course->teacher->name ?? 'N/A',
                    'Number_of_students' => $course->users->count(),
                ];

                $this->totalCourses++;
                $this->totalTeachers += $course->teacher ? 1 : 0;
                $this->totalStudents += $course->users->count();
            }

            $this->totalCategories++;
        }

        // Add the totals row at the end of the data
        $this->data[] = [
            'course_name' => 'Total :'.$this->totalCourses,
            'category_name' => 'Total :'.$this->totalCategories,
            'teacher_name' => 'Total :'.$this->totalTeachers,
            'Number_of_students' => 'Total :'.$this->totalStudents,
        ];
    }

    /**
     * Return the array of data to export.
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'Course Name',
            'Category Name',
            'Teacher Name',
            'Number_of_students',
        ];
    }

    /**
     * Register events for customization.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FF0000'], 
                        'size' => 14, 
                    ],
                ]);
                 // Auto-size all columns
                 foreach (range('A', 'D') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Adjust row heights
                $sheet->getRowDimension(1)->setRowHeight(25); // Headings row
                $totalRows = count($this->data) + 1;
                for ($i = 2; $i <= $totalRows; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }

                $lastRow = count($this->data) + 1; 

                // Apply bold styling to the totals row
                $sheet->getStyle("A{$lastRow}:D{$lastRow}")->getFont()->setBold(true);
             
            },
        ];
    }
}

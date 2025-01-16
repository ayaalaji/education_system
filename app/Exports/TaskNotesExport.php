<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TaskNotesExport implements FromArray , WithStyles
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
    /**
    * @return array
    */
    public function array(): array
    {
        $data = $this->task->users->map(function ($user) {
            return [
                $user->name,
                $user->pivot->note ?? 'N/A', 
                $user->pivot->grade ?? 'N/A',
                $this->task->title, 
                $this->task->due_date->format('Y-m-d'), 
                $this->task->status, 
            ];
        })->toArray();
    
        // add the name of coloums in the table
        return array_merge([
            ['Student Name', 'Note', 'Grade', 'Task Title', 'Due Date', 'Status']
        ], $data);

    }

    /**
     * تطبيق التنسيقات باستخدام WithStyles
     */
    public function styles($sheet)
    {
        // Format the headers (first row)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // لون النص أبيض
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'], // لون الخلفية أخضر
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // محاذاة أفقية
                'vertical' => Alignment::VERTICAL_CENTER, // محاذاة عمودية
            ],
        ]);

        // تنسيق حدود الخلايا
        $sheet->getStyle('A1:F' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // حدود رفيعة
                    'color' => ['rgb' => '000000'], // لون الحدود أسود
                ],
            ],
        ]);

        // ضبط عرض الأعمدة بناءً على أطول محتوى
        foreach (range('A', 'F') as $column) {
            $maxLength = 0;

            // المرور عبر جميع الصفوف لحساب أطول محتوى في كل عمود
            foreach ($sheet->getRowIterator() as $row) {
                $cellValue = $sheet->getCell($column . $row->getRowIndex())->getValue();
                if ($cellValue !== null) {
                    $maxLength = max($maxLength, strlen((string) $cellValue));
                }
            }

            // ضبط عرض العمود وإضافة هامش
            $sheet->getColumnDimension($column)->setWidth($maxLength + 2); // إضافة هامش
        }

        return [];
    }
}

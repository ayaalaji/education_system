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
     * Formate the row in Excel
     */
    public function styles($sheet)
    {
        // Format the headers (first row)
        $sheet->getStyle('A1:F1')->applyFromArray([
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
        $sheet->getStyle('A1:F' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Thin border style
                    'color' => ['rgb' => '000000'], // Set border color to black
                ],
            ],
        ]);

        // Automatically adjust column width based on the content
        foreach (range('A', 'F') as $column) {
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

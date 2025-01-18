<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;;

class UsersWithOverdueTasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * Fetch the users with overdue tasks and include task details.
     */
    public function collection()
    {

      //Get users with overdue tasks, and not completed
      $tasks = Task::where('due_date', '<', now())
      ->where('status', 'UnComplete')
      ->get();

      //Get for every task the user that didnt deliver the task before due_date
      $result = $tasks->map(function ($task)  
      {
        $usersWhoSubmit = $task->users()->pluck('users.id')->toArray(); 

        $missingUsers = User::whereIn('id', User::pluck('id')->diff($usersWhoSubmit))->get();
        $missingUserDetails = $missingUsers->map(function ($user) {
            return $user->name . ' (' . $user->email . ')';
        })->toArray();

          return [
              'task_id' => $task->id,
              'task_title' => $task->title,
              'missing_user_details' => implode(', ', $missingUserDetails)
              ];
      });
        //Make the resolt as collection
    return collect($result);

    }

    /**
     * Define the Excel file headings.
     */
    public function headings(): array
    {
        return [
            'Task ID', 
            'Task Title', 
            'Missing Users (Name and Email)'  
        ];
    }

    /**
     * Map the data for each row in the Excel file.
     */
    public function map($row): array
    {
        return [
            $row['task_id'],              
            $row['task_title'],            
            $row['missing_user_details'],    
        ];
    }

    public function styles($sheet)
    {
        // Calculate the max length of the content in column 'C' (Missing Users)
        $maxLength = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('C' . $row->getRowIndex())->getValue();
            $maxLength = max($maxLength, strlen($cellValue));
        }
        // تنسيق الـ Heading
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFont()->setSize(12);
        $sheet->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFEB9C'); // لون خلفية الأصفر

        // تحديد حدود الأعمدة
        $sheet->getStyle('A1:C' . ($sheet->getHighestRow()))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // محاذاة النصوص في المنتصف (عرضياً وعمودياً)
        $sheet->getStyle('A1:C' . ($sheet->getHighestRow()))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C' . ($sheet->getHighestRow()))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // عرض الأعمدة
        $sheet->getColumnDimension('A')->setWidth(15);  // Width for Task ID
        $sheet->getColumnDimension('B')->setWidth(25);  // Width for Task Title
        $sheet->getColumnDimension('C')->setWidth($maxLength + 5);  // Width for Missing Users

        // تجميد الصف العلوي
        $sheet->freezePane('A2');

        return $sheet;
    }
}

<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TaskNotesExport implements FromArray
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


}






    


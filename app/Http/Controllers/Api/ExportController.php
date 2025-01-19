<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\ExportService;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    protected $exportService;
    /**
     * Summary of __construct
     * @param \App\Services\ExportService $exportService
     */
    public function __construct(ExportService $exportService)
    {
        $this->middleware('security');
        $this->exportService =$exportService;
    }

     /**
     * Generate and save an Excel file for the task with students' notes and grades.
     * @param mixed $taskId
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function generateExcel($taskId)
    {
        $filePath = $this->exportService->generateExcel($taskId);

        return response()->json([
            'message' => 'Excel file has been saved successfully!',
            'file_path' => $filePath,
        ]);
    }

    //.....................................User With Overdue Tasks........................................

    /**
     * generate an Excel for export Users With Overdue Tasks
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function exportUsersWithOverdueTasks()
    {
        $this->exportService->exportUsersWithOverdueTasks();

        return $this->success(null,'Excel file has been saved successfully!');
        
    }

    //.....................................Course Report Export....................................

    public function exportCourseReport(Course $course)
    {
        $this->exportService->exportCourseReport($course);

        return $this->success(null,'Excel file has been saved successfully!');  
    }


 //.....................................Education System Export....................................

    public function exportEducationSystem()
{
    $this->exportService->exportEducationSystem();

    return $this->success(null,'Excel file has been saved successfully!');  
}
}

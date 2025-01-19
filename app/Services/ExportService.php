<?php

namespace App\Services;

use Exception;
use App\Models\Task;
use App\Exports\TaskNotesExport;
use App\Exports\CourseReportExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EducationSystemExport;
use App\Exports\UsersWithOverdueTasksExport;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExportService {
    /**
     * export To Desktop
     * @param mixed $exportClass
     * @param mixed $filePrefix
     * @throws \Exception
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function exportToDesktop($exportClass, $filePrefix)
    {
        try {
            // Dynamically get the desktop path based on the OS
            $desktopPath = $this->getDesktopPath();
    
            // Ensure the path exists
            if (!is_dir($desktopPath)) {
                mkdir($desktopPath, 0777, true);
            }
    
            // Define the file name and full path
            $fileName = $filePrefix . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filePath = $desktopPath . '/' . $fileName;
    
            // Export the data to an Excel file at the specified path
            Excel::store( $exportClass, $fileName, 'local');
    
            // Move the file from temporary storage to the desktop
            $storedPath = storage_path('app/' . $fileName);
            if (file_exists($storedPath)) {
                rename($storedPath, $filePath);
            } else {
                throw new Exception('File not found in temporary storage.');
            }
    
            return response()->json([
                'message' => 'Excel file has been saved successfully!',
                'file_path' => $filePath,
            ]);
    
        } catch (Exception $e) {
            Log::error('Error Export Excel: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to export file ' ], 500));
        }
    }
    
    //...................................
    /**
     * Get the user's desktop path dynamically
     * @throws \Exception
     * @return string
     */
    private function getDesktopPath()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return getenv('USERPROFILE') . '\Desktop';
        } elseif (PHP_OS_FAMILY === 'Linux' || PHP_OS_FAMILY === 'Darwin') { // Darwin is for macOS
            return getenv('HOME') . '/Desktop';
        } else {
            throw new Exception('Unsupported operating system.');
        }

    }
      /**
     * Generate an Excel for export Users With Overdue Tasks
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function exportUsersWithOverdueTasks()
    {
        try {
            return $this->exportToDesktop(new UsersWithOverdueTasksExport(), 'overdue_tasks');

        } catch (Exception $e) {
            Log::error('Error Export Excel: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to export file '], 500));
        }
    }

    //................................

    /**
     * Generate and save an Excel file for the task with students' notes and grades.
     * @param mixed $taskId
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function generateExcel($taskId)
    {
        try {
             // Retrieve the task by its ID
             $task = Task::findOrFail($taskId);
            
             return $this->exportToDesktop(new TaskNotesExport($task), 'task_notes');
        } catch (Exception $e) {
            Log::error('Error Export Excel: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to export file: '], 500));
        }
      
    }

    public function exportCourseReport($course)
    {
        try {
            return $this->exportToDesktop(new CourseReportExport($course->id), 'course_report.xlsx');
        } catch (Exception $e) {
            Log::error('Error Export Excel: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to export file: '], 500));
        }
    }
    
    public function exportEducationSystem()
    {
        try {
            return $this->exportToDesktop(new EducationSystemExport, 'Education_System.xlsx');
        } catch (Exception $e) {
            Log::error('Error Export Excel: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to export file: '], 500));
        }
    }
}
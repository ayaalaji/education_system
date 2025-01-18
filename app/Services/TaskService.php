<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\TaskNotesExport;
use App\Mail\TaskEvaluationMail;
use App\Events\TaskSubmittedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Exports\UsersWithOverdueTasksExport;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class TaskService
{
    public function listAllTask(array $filters, int $perPage, int $courseId)
    {
        $cacheKey = 'tasks_' . md5(json_encode($filters) . $perPage . request('page', 1));

        return cacheData($cacheKey, function () use ($filters, $perPage, $courseId) {
            $status = $filters['status'] ?? null;

            return Task::when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
                ->where('course_id', $courseId)
                ->with(['users', 'course'])
                ->paginate($perPage);
        });
    }

    public function createTask(array $data)
    {
        return DB::transaction(function () use ($data) {
            $task = Task::create($data);
            $this->assignTaskToCourseStudents($task, $data);
            cache()->forget('tasks_' . md5(json_encode([]) . request('page', 1)));
            return $task;
        });
    }

    public function getTask(Task $task)
    {
        return cacheData("task_{$task->id}", function () use ($task) {
            return $task->load(['users', 'course']);
        });
    }

    public function updateTask(array $data, Task $task)
    {
        $task->update(array_filter($data));
        Cache::forget("task_{$task->id}");
        return $task;
    }

    public function deleteTask(Task $task)
    {
        Cache::forget("task_{$task->id}");
        $task->delete();
    }

    private function assignTaskToCourseStudents(Task $task, array $data)
    {

        $students = User::whereHas('courses', function ($query) use ($data) {
            $query->where('courses.id', $data['course_id']);
        })->get();

        foreach ($students as $student) {
            $task->users()->attach($student->id, [
                'task_id' => $task->id,
                'file_path' => null,

            ]);
        }
    }


    public function addAttachment(Task $task, Request $request)
    {

        if (!$request->hasFile('file_path')) {
            throw new Exception('No file was uploaded.', 400);
        }


        $file = $request->file('file_path');
        $originalName = $file->getClientOriginalName();

        // Ensure the file extension is valid and there is no path traversal in the file name
        if (preg_match('/\.[^.]+\./', $originalName)) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }

        // Check for path traversal attack
        if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.pathTraversalDetected'), 403);
        }


        $allowedMimeTypes = [
            'application/pdf',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        $mime_type = $file->getClientMimeType();

        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }


        try {
            $response = Http::withHeaders([
                'x-apikey' => env('VIRUSTOTAL_API_KEY')
            ])->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $originalName
            )->post('https://www.virustotal.com/api/v3/files');


            if ($response->failed()) {
                throw new Exception(trans('general.virusScanFailed'), 500);
            }

            $scanResult = $response->json();


            if (
                isset($scanResult['data']['attributes']['last_analysis_stats']['malicious']) &&
                $scanResult['data']['attributes']['last_analysis_stats']['malicious'] > 0
            ) {
                throw new FileException(trans('general.virusDetected'), 403);
            }
        } catch (Exception $e) {
            Log::error('Error during virus scanning: ' . $e->getMessage());
            throw new Exception(trans('general.virusScanFailed'), 500);
        }

        // Generate a safe, random file name
        $fileName = Str::random(32);
        $extension = $file->getClientOriginalExtension(); // Safe way to get file extension
        $filePath = "Files/{$fileName}.{$extension}";


        $path = Storage::disk('local')->putFileAs('Files', $file, $fileName . '.' . $extension);


        $url = Storage::disk('local')->url($path);


        $user = auth()->user();

        $isEnrolledInTask = $task->users()->where('student_id', $user->id)->exists(); // تحقق من وجود الطالب في الربط مع المهمة

        if (!$isEnrolledInTask) {
            throw new Exception('The user is not assigned to this task.', 403);
        }

        $task->users()->updateExistingPivot($user->id, [
            'file_path' => $url,
            'summation_date' => Carbon::now(),
        ]);

        return response()->json(['message' => 'File uploaded and task assigned successfully']);
    }

    /**
     * Summary of forceDeleteTask
     * @param mixed $id
     * @return void
     */
    public function forceDeleteTask($id)
    {
         $arry_of_deleted_taskes = Task::onlyTrashed()->pluck('id')->toArray();
        if (in_array($id, $arry_of_deleted_taskes)) {
            $task = Task::onlyTrashed()->find($id);
            $task->forceDelete();
        }
    }
    /**
     * Summary of restoreTask
     * @param mixed $id
     * @return array|mixed|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     */
    public function restoreTask($id)
    {
        $task = Task::onlyTrashed()->find($id);
        $task->restore();
        return $task;
    }

    //..................................Export File.............................
  
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

    //..............................................
    
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
            throw new HttpResponseException(response()->json(['message' => 'Failed to export file: ' . $e->getMessage()], 500));
        }
      
    }

}

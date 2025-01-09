<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\TaskSubmittedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TaskService
{
    public function listAllTask(array $filters, int $perPage, int $courseId)
    {
        $cacheKey = 'tasks_' . md5(json_encode($filters) . $perPage . request('page', 1));

    return cacheData($cacheKey, function () use ($filters, $perPage, $courseId) {
        $status = $filters['status'] ?? null;

        return Task::when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->where('course_id', $courseId) // استخدام course_id الممرر من الميدلوير
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
            'application/pdf', 'text/plain',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword', 'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg', 'image/png', 'image/gif', 'image/webp'
        ];

        $mime_type = $file->getClientMimeType();

        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }


        try {
            $response = Http::withHeaders([
                'x-apikey' => env('VIRUSTOTAL_API_KEY')
            ])->attach(
                'file', file_get_contents($file->getRealPath()), $originalName
            )->post('https://www.virustotal.com/api/v3/files');


            if ($response->failed()) {
                throw new Exception(trans('general.virusScanFailed'), 500);
            }

            $scanResult = $response->json();


            if (isset($scanResult['data']['attributes']['last_analysis_stats']['malicious']) &&
                $scanResult['data']['attributes']['last_analysis_stats']['malicious'] > 0) {
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


}


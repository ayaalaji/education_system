<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    public function listAllTask(array $filters, int $perPage)
    {
        $cacheKey = 'tasks_' . md5(json_encode($filters) . $perPage . request('page', 1));

        return cacheData($cacheKey, function () use ($filters, $perPage) {
            $status = $filters['status'] ?? null;

            return Task::when($status, function($query) use ($status){
                return $query->where('status',$status);
            })
                ->with(['users','course'])
                ->paginate($perPage);
        });
    }

    public function createTask(array $data)
    {
        return DB::transaction(function () use ($data) {
            $task = Task::create($data);
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

    public function assignTaskToUser(Task $task, array $data)
    {
        $user = User::findOrFail($data['student_id']);
        $task->users()->attach($user, [
            'task_id' => $data['task_id'],
            'file_path' => $data['file_path'],
            'summation_date' => $data['summation_date'],
        ]);
    }
}


<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTaskIsForUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       // جلب كائن المهمة من الراوت
    $task = $request->route('task');

    // التحقق إذا لم يتم العثور على المهمة
    if (!$task) {
        return response()->json(['error' => 'Task not found'], 404);
    }

    // التحقق من أن المستخدم الحالي مرتبط بالمهمة
    if (!$task->users()->where('student_id', auth('api')->id())->exists()) {
        return response()->json(['error' => 'This task is not assigned to you'], 403);
    }

    return $next($request);
    }
}

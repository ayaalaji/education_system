<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Course;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCourseTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $courseId = $request->input('course_id');

        if (!$courseId) {
            return response()->json(['error' => 'Course ID is required'], 400);
        }

        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        // التحقق من أن المعلم مسؤول عن هذا الكورس
        if ($course->teacher_id !== auth('teacher-api')->id()) {
            return response()->json(['error' => 'You are not authorized to manage tasks for this course'], 403);
        }

        // إضافة course_id إلى الـ request ليتم استخدامه في الـ controller
        $request->merge(['course_id' => $courseId]);

        return $next($request);
    }


}
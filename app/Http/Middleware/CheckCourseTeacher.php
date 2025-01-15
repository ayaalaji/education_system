<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Course;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $course = $request->route('course');
      
        // Check if the course belongs to the authenticated teacher
        if (  auth('teacher-api')->id() != $course->teacher_id) 
        {
            return response()->json(['error' => 'This course is not assigned to you'], 403);
        }
    
        // If all checks pass, proceed to the next middleware or controller
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaterialTeaherOfTheCourse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $material = $request->route('material');
        
        if($material->course->teacher_id == auth('teacher-api')->id())
        {
            return $next($request);
        }else{
            return response()->json(['error' => 'You are not authorized to manage this material'], 403);
        }
        
        
    }
}

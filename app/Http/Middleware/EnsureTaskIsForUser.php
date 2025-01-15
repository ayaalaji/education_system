<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
    /** @test */
public function it_can_upload_attachment_for_assigned_task()
{
    // إنشاء مستخدم طالب
    $student = User::factory()->create();
    $teacher = User::factory()->create();

    // إنشاء كورس
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء مهمة مربوطة بالكورس
    $task = Task::factory()->create(['course_id' => $course->id]);

    // ربط الطالب بالمهمة
    $task->users()->attach($student->id, ['student_id' => $student->id]);

    // محاكاة تسجيل الدخول كطالب
    $this->actingAs($student, 'api');

    // محاكاة رفع الملف
    $file = UploadedFile::fake()->create('document.pdf', 100); // 100 KB pdf file
    $response = $this->postJson(route('tasks.addAttachment', $task->id), [
        'file_path' => $file,
    ]);

    // التحقق من نجاح الرفع
    $response->assertStatus(200)
             ->assertJson(['message' => 'File uploaded and task assigned successfully']);
}

/** @test */
public function it_cannot_upload_attachment_for_unassigned_task()
{
    // إنشاء مستخدم طالب آخر لا ينتمي لهذه المهمة
    $student = User::factory()->create();
    $teacher = User::factory()->create();

    // إنشاء كورس
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء مهمة
    $task = Task::factory()->create(['course_id' => $course->id]);

    // محاكاة تسجيل الدخول كطالب غير مرتبط بالمهمة
    $this->actingAs($student, 'api');

    // محاكاة رفع الملف
    $file = UploadedFile::fake()->create('document.pdf', 100); // 100 KB pdf file
    $response = $this->postJson(route('tasks.addAttachment', $task->id), [
        'file_path' => $file,
    ]);

    // التحقق من أنه لا يمكن للطالب رفع الملف لأنه غير مرتبط بالمهمة
    $response->assertStatus(403)
             ->assertJson(['error' => 'This task is not assigned to you']);
}

}

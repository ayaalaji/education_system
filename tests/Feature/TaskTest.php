<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Mail\TaskEvaluationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;

    public function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->artisan('db:seed');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

    /** @test */
    public function it_can_list_all_tasks_for_teacher_course()
    {
        $admin = Teacher::factory()->create(['email' => fake()->unique()->safeEmail()]);

    // إنشاء الدورة
    $course = Course::factory()->create(['teacher_id' => $admin->id]);

    // توليد التوكن
    $token = JWTAuth::fromUser($admin);

    // استدعاء API
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/task?course_id={$course->id}");

    // التحقق من الاستجابة
    $response->assertStatus(200);
    }


    /** @test */
    public function it_can_create_a_task_for_teacher_course()
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $course = Course::factory()->create(['teacher_id' => $admin->id]);
        $data=[
            'title' => 'first task',
            'due_date' => '2025-1-19',
            'status' => 'Complete',
            'course_id' => $course->id,
            'notes' => 'simple notes'
        ];

        $token = JWTAuth::fromUser($admin);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/task?course_id={$course->id}", $data);


        $response->assertStatus(201);

    }
/** @test */
public function it_allows_to_retrieve_task_with_users_and_course()
{
    // العثور على المعلم باستخدام البريد الإلكتروني
    $teacher = Teacher::where('email', 'admin@gmail.com')->first();
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء مهمة مرتبطة بالكورس
    $task = Task::factory()->create(['course_id' => $course->id]);

    // إضافة مستخدم إلى المهمة
    $user = User::factory()->create();
    $task->users()->attach($user->id);

    // محاكاة تسجيل الدخول كمعلم
    $token = JWTAuth::fromUser($teacher);
    $this->withHeader('Authorization', 'Bearer ' . $token);

    // إرسال طلب GET لاسترجاع المهمة
    $response = $this->getJson("/api/task/{$task->id}?course_id={$course->id}");

    // التحقق من حالة الاستجابة
    $response->assertStatus(200);


}




    /** @test */
    public function it_can_update_a_task_for_teacher_course()
    {
        // العثور على المعلم من خلال البريد الإلكتروني
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        // إنشاء كورس جديد مربوط بالمعلم
        $course = Course::factory()->create(['teacher_id' => $admin->id]);

        // البيانات لإنشاء المهمة
        $data = [
            'title' => 'first task',
            'due_date' => '2025-1-19',
            'status' => 'Complete',
            'course_id' => $course->id,
            'notes' => 'simple notes'
        ];

        // إرسال طلب لإنشاء مهمة جديدة
        $token = JWTAuth::fromUser($admin);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson("/api/task?course_id={$course->id}", $data);

        // التحقق من استجابة الإنشاء
        $response->assertStatus(201); // تحقق من أن المهمة تم إنشاؤها بنجاح

        // التحقق من محتوى الاستجابة
        $responseData = $response->json();
        Log::info('Response Data:', $responseData);  // تسجيل الاستجابة لمراجعتها

        // تحقق من وجود data في الاستجابة
        if (empty($responseData['data'])) {
            $this->fail('Task creation failed or task ID is missing');
        }

        // محاولة الحصول على ID المهمة التي تم إنشاؤها
        $taskId = $responseData['data']['id'] ?? null;

        // تحقق من أن المهمة تم إنشاؤها
        if (!$taskId) {
            $this->fail('Task creation failed or task ID is missing');
        }

        // بيانات التحديث للمهمة
        $updateData = ['status' => 'UnComplete'];

        // تحديث المهمة
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                               ->putJson("/api/task/{$taskId}?course_id={$course->id}", $updateData);

        // التحقق من الاستجابة للتحديث
        $updateResponse->assertStatus(200)
                       ->assertJson(['message' => 'task update success']);
    }










   /** @test */
public function it_can_delete_a_task_for_teacher_course()
{
    // العثور على المعلم من خلال البريد الإلكتروني
    $admin = Teacher::where('email', 'admin@gmail.com')->first();

    // إنشاء كورس جديد مربوط بالمعلم
    $course = Course::factory()->create(['teacher_id' => $admin->id]);

    // إنشاء مهمة مرتبطة بالكورس
    $task = Task::factory()->create(['course_id' => $course->id]);

    // إرسال طلب لحذف المهمة
    $token = JWTAuth::fromUser($admin);
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/task/{$task->id}?course_id={$course->id}");

    // التحقق من الاستجابة (204: تم الحذف بنجاح)
    $response->assertStatus(204);

    // التحقق من أن المهمة قد تم حذفها
    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
}




    /** @test */
    /** @test */
public function it_allows_teacher_to_store_note_for_assigned_task()
{
    // العثور على المعلم من خلال البريد الإلكتروني
    $teacher = Teacher::where('email', 'admin@gmail.com')->first();

    // إنشاء كورس مرتبط بالمعلم
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء طالب و ربطه بالكورس
    $student = User::factory()->create();
    $task = Task::factory()->create(['course_id' => $course->id]);
    $task->users()->attach($student->id);

    // محاكاة تسجيل الدخول كمعلم يملك الكورس
    $token = JWTAuth::fromUser($teacher);
    $this->withHeader('Authorization', 'Bearer ' . $token);

    // إعداد البيانات لتخزين الملاحظة
    $data = [
        'note' => 'Good performance.',
        'grade' => 9
    ];

    // محاكاة إرسال البريد الإلكتروني
    Mail::fake();

    // إرسال طلب POST لتخزين الملاحظة
    // /{taskId}/users/{userId}/add-note
    $response = $this->postJson("/api/tasks/{$task->id}/users/$student->id/add-note?course_id={$course->id}", $data);

    // التحقق من نجاح العملية
    $response->assertStatus(201)
             ->assertJson(['message' => 'note added success']);

    // التحقق من إرسال البريد الإلكتروني
    Mail::assertSent(TaskEvaluationMail::class, function ($mail) use ($student) {
        return $mail->hasTo($student->email);
    });
}



/** @test */
public function it_allows_teacher_to_remove_note_for_assigned_task()
{
    // العثور على المعلم من خلال البريد الإلكتروني
    $teacher = Teacher::where('email', 'admin@gmail.com')->first();

    // إنشاء كورس مرتبط بالمعلم
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء طالب و ربطه بالكورس والمهمة
    $student = User::factory()->create();
    $task = Task::factory()->create(['course_id' => $course->id]);
    $task->users()->attach($student->id, ['note' => 'Good performance.', 'grade' => 3]); // إضافة ملاحظة للمهمة

    // محاكاة تسجيل الدخول كمعلم يملك الكورس
    $token = JWTAuth::fromUser($teacher);
    $this->withHeader('Authorization', 'Bearer ' . $token);

    // إرسال طلب DELETE لحذف الملاحظة
    $response = $this->deleteJson("/api/tasks/{$task->id}/users/{$student->id}/delete-note?course_id={$course->id}");

    // التحقق من نجاح العملية
    $response->assertStatus(204);

    // التحقق من أن الملاحظة تم حذفها
    $task = Task::findOrFail($task->id);
    $pivotData = $task->users()->where('student_id', $student->id)->first()->pivot;
    $this->assertNull($pivotData->note, 'The note was not removed.');
}



/** @test */
public function it_allows_forced_deletion_of_a_task_for_teacher()
{
    // العثور على المعلم من خلال البريد الإلكتروني
    $teacher = Teacher::where('email', 'admin@gmail.com')->first();

    // محاكاة تسجيل الدخول كمعلم
    $token = JWTAuth::fromUser($teacher);
    $this->withHeader('Authorization', 'Bearer ' . $token);

    // إنشاء كورس مرتبط بالمعلم
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء مهمة مرتبطة بالكورس
    $task = Task::factory()->create(['course_id' => $course->id]);
    $task->delete(); // حذف المهمة بشكل ناعم

    // استدعاء الوظيفة الخاصة بحذف المهمة بشكل دائم
    $response = $this->deleteJson("/api/task/{$task->id}/forcedelete?course_id={$course->id}");



    // التحقق من استجابة الحذف
    $response->assertStatus(200);
}



/** @test */
public function it_allows_teacher_to_restore_task()
{
    // العثور على المعلم من خلال البريد الإلكتروني
    $teacher = Teacher::where('email', 'admin@gmail.com')->first();

    // إنشاء كورس مرتبط بالمعلم
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    // إنشاء مهمة مرتبطة بالكورس
    $task = Task::factory()->create(['course_id' => $course->id]);
    $task->delete(); // حذف المهمة بشكل ناعم

    // محاكاة تسجيل الدخول كمعلم
    $token = JWTAuth::fromUser($teacher);
    $this->withHeader('Authorization', 'Bearer ' . $token);

    // إرسال طلب استرجاع المهمة
    $response = $this->postJson("/api/task/{$task->id}/restore?course_id={$course->id}");

    // التحقق من أن المهمة تم استرجاعها بنجاح
    $response->assertStatus(200);


}


}

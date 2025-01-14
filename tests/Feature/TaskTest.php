<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
       
        $this->teacher = Teacher::factory()->create([
            'email' => 'teacher@gmail.com',
        ]);
        $this->course = Course::factory()->create([
            'teacher_id' => $this->teacher->id,
        ]);
    }

    /**
     * Test the method store (create a new task).
     */
    public function test_create_task(): void
    {
        $token = JWTAuth::fromUser($this->teacher);

        $data = [
            'title' => 'Task 1234567',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'UnComplete',
            'course_id' => $this->course->id,
            'notes' => 'This is a test note.',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['title', 'due_date', 'status', 'course_id', 'notes']]);
    }

    /**
     * Test the method index.
     */
    public function test_get_all_tasks(): void
    {
        $token = JWTAuth::fromUser($this->teacher);


        Task::factory()->count(3)->create([
            'course_id' => $this->course->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tasks');



        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['title', 'due_date', 'status', 'course_id', 'notes']]]);


    }

    /**
     * Test the method show (retrieve a specific task by ID).
     */
    public function test_show_specific_task(): void
    {
        $token = JWTAuth::fromUser($this->teacher);


        $task = Task::factory()->create([
            'course_id' => $this->course->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['title', 'due_date', 'status', 'course_id', 'notes']]);
    }

    /**
     * Test the method update (update a specific task).
     */
    public function test_update_specific_task(): void
    {
        $token = JWTAuth::fromUser($this->teacher);


        $task = Task::factory()->create([
            'course_id' => $this->course->id,
        ]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'Complete',
            'course_id' => $this->course->id,
            'notes' => 'Updated notes for the task.',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $task->id, 'title' => 'Updated Task Title']]);
    }

    /**
     * Test the method delete (delete a specific task).
     */
    public function test_delete_specific_task(): void
    {
        $token = JWTAuth::fromUser($this->teacher);


        $task = Task::factory()->create([
            'course_id' => $this->course->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
    }

     /**
     * Test adding a note to a specific task.
     */
    public function testStoreNote()
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();
        $task->users()->attach($user->id);

        $data = [
            'note' => 'Excellent performance',
            'grade' => 95,
        ];

        Mail::fake();

        $response = $this->postJson("/api/tasks/{$task->id}/notes/{$user->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'student_id' => $user->id,
            'note' => 'Excellent performance',
            'grade' => 95,
        ]);

        Mail::assertSent(function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test removing a note from a specific task.
     */
    public function testRemoveNote()
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();
        $task->users()->attach($user->id, [
            'note' => 'Good performance',
            'grade' => 85,
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}/notes/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'student_id' => $user->id,
            'note' => null,
        ]);
    }

    /**
     * Test uploading a file and attaching it to a task.
     */
    public function testAddAttachment()
    {
        Storage::fake('local');

        $task = Task::factory()->create();
        $user = User::factory()->create();
        $task->users()->attach($user->id);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson("/api/tasks/{$task->id}/attachments", [
            'file_path' => $file,
        ]);

        $response->assertStatus(200);
        Storage::disk('local')->assertExists("Files/{$file->hashName()}");
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'student_id' => $user->id,
            'file_path' => Storage::disk('local')->url("Files/{$file->hashName()}"),
        ]);
    }

    /**
     * Test force deleting a task.
     */
    public function testForceDeleteTask()
    {
        $task = Task::factory()->create();
        $task->delete();

        $response = $this->deleteJson("/api/tasks/force-delete/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test restoring a soft-deleted task.
     */
    public function testRestoreTask()
    {
        $task = Task::factory()->create();
        $task->delete();

        $response = $this->patchJson("/api/tasks/restore/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deleted_at' => null,
        ]);
    }
}

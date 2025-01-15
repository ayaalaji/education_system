<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Testing\TestResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class NoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed'); 

        $this->user = User::where('email', 'samer@gmail.com')->first();
        $this->task = Task::where('title', 'Complete Chapter 1')->first();
        $this->teacher = Teacher::where('email', 'admin@gmail.com')->first();
        if (!$this->teacher) {
            throw new \Exception("Teacher user not found for testing. Ensure your seed file creates an admin@gmail.com teacher.");
        }
        

    }


    public function test_add_note()
    {
        $noteData = [
            'note' => 'This is a test note',
            'grade' => 5,
        ];

        $response = $this->actingAsTeacher()->postJson("/api/notes/{$this->task->id}/users/{$this->user->id}/add-note", $noteData);
        $response->assertStatus(201);


        // Assert that the note was added correctly, using a more robust assertion
        $this->assertDatabaseHas('task_user', [
            'student_id' => $this->user->id,
            'task_id' => $this->task->id,
            'note' => 'This is a test note',
            'grade' => 5,
        ]);
    }


    public function test_delete_note(): void
    {
        //Add the note before deleting it.
        $this->task->users()->updateExistingPivot($this->user->id, ['note' => 'This is a note', 'grade' => 9]);

        $response = $this->actingAsTeacher()->deleteJson("/api/notes/{$this->task->id}/users/{$this->user->id}/delete-note");
        $response->assertStatus(204); //Or assertStatus(204) if your API returns 204 on successful delete

        // Assert that the note was deleted
        $this->assertDatabaseMissing('task_user', [
            'student_id' => $this->user->id,
            'task_id' => $this->task->id,
            'note' => 'This is a note',
        ]);
    }



    //Helper function to simplify authentication
    private function actingAsTeacher(): self
    {
        try {
            $token = JWTAuth::fromUser($this->teacher);
            return $this->withHeader('Authorization', 'Bearer ' . $token);
        } catch (ModelNotFoundException $e) {
            $this->fail("Teacher user not found for testing. Check your database seeding.");
        } catch (\Exception $e) {
            $this->fail("Error getting teacher token: " . $e->getMessage());
        }
    }
}
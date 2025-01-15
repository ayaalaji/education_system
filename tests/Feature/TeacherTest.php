<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Teacher;
use App\Models\Role; 
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;


class TeacherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('migrate:fresh --seed'); 
    }

    /**
     * Test the method index (get all teachers).
     */
    public function test_get_all_teachers(): void
    {
        $this->actingAsAdmin()->getJson('/api/teachers')->assertStatus(200);
    }

    /**
     * Test the method store (create a new teacher).
     */
    public function test_create_teacher(): void
    {
        $data = [
            'name' => 'hanen',
            'email' => 'hanen@test.com', 
            'specialization' => 'program language',
            'password' => 'password',
            'role' => 'teacher',
        ];

        $this->actingAsAdmin()->postJson('/api/teachers', $data)->assertStatus(201);
    }

    /**
     * Test the method show (retrieve a specific teacher by ID).
     */
    public function test_show_specific_teacher(): void
    {
        $teacher = Teacher::factory()->create(); // Use factory for cleaner creation
        $this->actingAsAdmin()->getJson("/api/teachers/{$teacher->id}")->assertStatus(200);
    }

    /**
     * Test the method update (update a specific teacher).
     */
    public function test_update_specific_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $data = [
            'name' => 'Updated Name',
            'specialization' => 'Updated Specialization',
        ];
        $this->actingAsAdmin()->putJson("/api/teachers/{$teacher->id}", $data)->assertStatus(200);
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id, 'name' => 'Updated Name']);
    }

    /**
     * Test the method destroy (delete a specific teacher).
     */
    public function test_delete_specific_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $this->actingAsAdmin()->deleteJson("/api/teachers/{$teacher->id}")->assertStatus(200);
    }

    //Helper function to simplify authentication
    private function actingAsAdmin(): self
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        if(!$admin){
            throw new \Exception("Admin user not found for testing");
        }
        $token = JWTAuth::fromUser($admin);
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}

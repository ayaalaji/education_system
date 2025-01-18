<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->artisan('db:seed');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Test fetching all categories.
     */
    public function test_index_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

      
        Category::create(['name' => 'Category 1', 'description' => 'Description 1', 'teacher_id' => $admin->id]);
        Category::create(['name' => 'Category 2', 'description' => 'Description 2', 'teacher_id' => $admin->id]);
        Category::create(['name' => 'Category 3', 'description' => 'Description 3', 'teacher_id' => $admin->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/categories');

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Categories fetched successfully.",
        ]);
    }

    /**
     * Test creating a category.
     */
    public function test_store_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

        $data = [
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/categories', $data);

        $response->assertStatus(201)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category created successfully!",
        ]);
    }

    /**
     * Test fetching a specific category.
     */
    public function test_show_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

  
        $category = Category::create([
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category details fetched successfully.",
        ]);
    }

    /**
     * Test updating a category.
     */
    public function test_update_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

      
        $category = Category::create([
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);

        $updatedData = ['name' => 'Updated Category'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/categories/{$category->id}", $updatedData);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category updated successfully!",
        ]);
    }

    /**
     * Test soft deleting a category.
     */
    public function test_soft_delete_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

        
        $category = Category::create([
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category deleted successfully!",
        ]);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    /**
     * Test fetching trashed categories.
     */
    public function test_trashed_categories(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

        $category = Category::create([
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);
        $category->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/categories/trashed');

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Trashed categories fetched successfully.",
        ]);
    }

    /**
     * Test restoring a soft-deleted category.
     */
    public function test_restore_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

      
        $category = Category::create([
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);
        $category->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/categories/{$category->id}/restore");

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category restored successfully!",
        ]);
    }

    /**
     * Test force deleting a soft-deleted category.
     */
    public function test_force_delete_category(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $token = JWTAuth::fromUser($admin);

        $category = Category::create([
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);
        $category->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/categories/{$category->id}/force-delete");

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category permanently deleted!",
        ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

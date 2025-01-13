<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
   // use RefreshDatabase;

    /**
     * Test fetching all categories.
     */
    public function test_index_category(): void
    {
        $admin = Teacher::factory()->create(); 
        Category::factory()->count(3)->create();
        $response = $this->actingAs($admin, 'api')->getJson('/api/categories');

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
        $admin = Teacher::factory()->create();

        $response = $this->actingAs($admin, 'api')->postJson('/api/categories', [
            'name'        => 'Test Category',
            'description' => 'This is a test category.',
            'teacher_id'  => $admin->id,
        ]);

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
        $admin = Teacher::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->getJson("/api/categories/{$category->id}");

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
        $admin = Teacher::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

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
        $admin = Teacher::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->deleteJson("/api/categories/{$category->id}");

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
        $admin = Teacher::factory()->create();
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($admin, 'api')->getJson('/api/categories-trashed');

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
        $admin = Teacher::factory()->create();
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($admin, 'api')->postJson("/api/categories/{$category->id}/restore");

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
        $admin = Teacher::factory()->create();
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($admin, 'api')->deleteJson("/api/categories/{$category->id}/force-delete");

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Category permanently deleted!",
        ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
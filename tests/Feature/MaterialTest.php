<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Teacher;
use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaterialTest extends TestCase
{
        use RefreshDatabase;
    
        protected function setUp(): void
        {
            parent::setUp();
            $this->artisan('migrate:fresh --seed');
        }
    
    /** @test */
    public function it_can_retrieve_all_materials()
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        // Arrange: Create sample materials
        Material::factory()->count(3)->create();

        // Act: Call the index endpoint
        $response = $this->actingAs($admin,'teacher-api')->getJson('api/materials');

        // Assert: Check if the response is successful and contains expected data
        $response->assertStatus(200);
                    
    }
    //.........................................................
    /** @test */
    public function it_can_create_a_material()
    {
        // Arrange: Prepare material data
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $video = UploadedFile::fake()->create('video.mp4', 500);

        $data = [
            'title' => 'Sample Material',
            'file_path' => $file,
            'video_path' => $video,
            'course_id' => 1,
        ];

        // Act: Call the store endpoint as an authenticated teacher
        $response = $this->actingAs($admin, 'teacher-api')->postJson('api/materials', $data);

        // Assert: Check if the material was created successfully
        $response->assertStatus(201);
              
    }

    //..........................
    



}

    


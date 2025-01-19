<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaterialTest extends TestCase
{
        use RefreshDatabase;
    
        protected function setUp(): void
        {
            parent::setUp();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
 
            $this->artisan('migrate:fresh --seed');
          
    
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
       /** @test */
       public function it_can_retrieve_a_single_material()
       {
           // Arrange: Create a sample material
           $admin = Teacher::where('email', 'admin@gmail.com')->first();
           $material = Material::factory()->create();
   
           // Act: Call the show endpoint as an authenticated teacher
           $response = $this->actingAs($admin, 'teacher-api')->getJson("api/materials/{$material->id}");
   
           // Assert: Check if the response is successful and contains expected data
           $response->assertStatus(200);
       }
//...................................................
    /** @test */
    public function it_can_update_a_material()
    {
        // Arrange: Create a sample material
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        $course = Course::create([
            'title' => 's',
            'description' => 'This is a test course.',
            'course_duration' =>45,
            'teacher_id' => $admin->id,
            'category_id' => $category->id, 
        ]);;  

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $video = UploadedFile::fake()->create('video.mp4', 500);
        
        $material = Material::create([
            'title' => 'Sample Material',
            'file_path' => $file,
            'video_path' => $video,
            'course_id' => $course->id,
           ]);  

        $data = ['title' => 'Updated Material Title'];

        // Act: Call the update endpoint as an authenticated teacher
        $response = $this->actingAs($admin, 'teacher-api')->putJson("api/materials/{$material->id}", $data);

        // Assert: Check if the response status is successful
        $response->assertStatus(200);
    }
//...................................................
      /** @test */
      public function it_can_delete_a_material()
      {
          $admin = Teacher::where('email', 'admin@gmail.com')->first();
          $material = Material::factory()->create();
          $response = $this->actingAs($admin, 'teacher-api')->deleteJson("api/materials/{$material->id}");
          $response->assertStatus(200);
      }

//..................................Soft Delete.................................................

    /** @test */
    public function it_can_force_delete_a_material()
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
        $material = Material::factory()->create();
         $material->delete(); // Soft delete first
        $response = $this->actingAs($admin, 'teacher-api')->deleteJson("api/materials/{$material->id}/forcedelete");
        $response->assertStatus(200);
    }

   //.........................................................
       /** @test */
       public function it_can_restore_a_material()
       {
           $admin = Teacher::where('email', 'admin@gmail.com')->first();
           $material = Material::factory()->create();
           $material->delete(); // Soft delete first
           $response = $this->actingAs($admin, 'teacher-api')->getJson("api/materials/{$material->id}/restore");
           $response->assertStatus(200);
       } 

       //.........................................................
       /** @test */
       public function it_can_permanently_delete_a_material()
       {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
           $material = Material::factory()->create();
           $material->delete(); 
           $response = $this->actingAs($admin, 'teacher-api')->getJson("api/materials/trashed");
           $response->assertStatus(200);
       
       }

}

    


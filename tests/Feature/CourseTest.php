<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use Nette\Utils\Random;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;


class CourseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test of Crud Couse
     */

     protected function setUp(): void
     {
         parent::setUp();
 
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
 
         $this->artisan('db:seed');
 
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');
 
     }   
    public function test_index_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
   

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('api/courses');


        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Fetch All Courses with the spicific filter Successfully",
        ]);
    }

    //................................

    public function test_store_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $response = $this->actingAs($admin,'teacher-api')->postJson('api/courses',[
            'title'           => 'test',
            'description'     => 'this is a test',
            'course_duration' => 15,
            'category_name'   =>'Programming'

        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "status"=> "success",
            "message"=> "Store Course Successfully",
        ]);
    }

    // //.................................

    public function test_show_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

           // Create a course record
        $course = Course::factory()->create([
            'title' => 's',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, 
        ]);

        $response = $this->actingAs($admin,'teacher-api')->getJson("api/courses/{$course->id}");
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Fetch Course Successfully",
        ]);
    }

    // //....................................
    public function test_update_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

           // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, 
        ]);

        $response = $this->actingAs($admin,'teacher-api')->putJson("api/courses/{$course->id}",[
            'title'   => 'edit course',
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Course Successfully",
        ]);
    }

    // //....................................
    public function test_soft_delete_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);

        $response = $this->actingAs($admin,'teacher-api')->deleteJson("api/courses/{$course->id}");
        

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Delete Course Successfully",
        ]);
    }

    // //................................. force delete , restore and trashed courses .....................

    public function test_force_delete_course(): void
    {
        // Authenticate as an admin (teacher with ID 1)
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
    
        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category ->id, // Adjust as per your categories table
        ]);
    
        // Soft delete the created course
        $course->delete();
    
        // Perform the force delete operation
        $response = $this->actingAs($admin, 'teacher-api')
            ->deleteJson("api/courses/{$course->id}/forcedelete");
    
        // Assert the response
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Force Deleted Course Successfully",
        ]);
    
        // Assert that the course is permanently deleted
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
    
    // //....................................
    public function test_restore_course(): void
    {
        // Authenticate as an admin (teacher with ID 1)
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
    
        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);
    
        // Soft delete the created course
        $course->delete();
    
        // Ensure the course is soft-deleted
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    
        // Perform the restore operation
        $response = $this->actingAs($admin, 'teacher-api')
            ->getJson("api/courses/{$course->id}/restore");
    
        // Assert the response
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Restore Course Successfully",
        ]);
    
        // Assert that the course is restored
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'deleted_at' => null]);
    }
    
    // //..................................
    public function test_trashed_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);
    
        // Soft delete the created course
        $course->delete();
        $response = $this->actingAs($admin,'teacher-api')->getJson('api/courses-trashed');

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get All Trashed Couses Successfully",
        ]);
    }

    // //...........................End Crud......................................

       public function test_update_status_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);

        $response = $this->actingAs($admin,'teacher-api')->putJson("api/courses/{$course->id}/updatestatus",[
            'status'   => 'Closed'
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Status Successfully",
        ]);
    }

    // //......................................

    public function test_update_start_date_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson("api/courses/{$course->id}/updateStartDate",[
            'start_date'   => '2025-01-25'
        ]);

      

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start Date Successfully",
        ]);
    }

    // //..........................................
    public function test_update_end_date_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'start_date'  => '2025-01-25',
            'course_duration' => 10,
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);

        $response = $this->actingAs($admin,'teacher-api')->putJson("api/courses/{$course->id}/updateEndDate",[
            'end_date'   => '2025-08-30'
        ]);

        

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update End Date Successfully", 
        ]);
    }

    // //...........................
    public function test_update_start_register_date_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson("api/courses/{$course->id}/updateStartRegisterDate",[

            'start_register_date'   => '2025-08-30'
        ]);

       

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start Register Date Successfully",
        ]);
    }

    // //..................................................
    public function test_update_end_register_date_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'start_register_date'   => '2025-08-30',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson("api/courses/{$course->id}/updateEndRegisterDate",[
            'end_register_date'   => '2025-09-01'
        ]);

        

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start Register Date Successfully",
        ]);
    }

    // //..................................................
    public function test_add_user_to_course(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $category = Category::factory()->create();

        $user = User::factory()->create();

        // Create a course record
        $course = Course::factory()->create([
            'title' => 'test',
            'description' => 'This is a test course.',
            'teacher_id' => $admin->id,
            'category_id' => $category->id, // Adjust as per your categories table
        ]);

        $response = $this->actingAs($admin,'teacher-api')->postjson("api/courses/{$course->id}/addUser",[
            'user' => $user->id
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }
}

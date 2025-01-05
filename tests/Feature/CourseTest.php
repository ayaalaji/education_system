<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseTest extends TestCase
{
    /**
     * Test of Crud Couse
     */
    public function test_index_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getjson('api/courses');

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Fetch All Courses with the spicific filter Successfully",
        ]);
    }

    //................................

    public function test_store_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->postJson('api/courses',[
            'title'           => 'test1',
            'description'     => 'this is a test',
            'course_duration' => 15,
            'category_name'   =>'cat 2'

        ]);
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"=> "success",
            "message"=> "Store Course Successfully",
        ]);
    }

    //.................................

    public function test_show_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getJson('api/courses/3');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Fetch Course Successfully",
        ]);
    }

    //....................................
    public function test_update_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->putJson('api/courses/6',[
            'title'           => 'test updated',
            'description'     => 'this is a test',
            'course_duration' => 15,
            'category_name'   =>'cat 2' 
        ]);
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Course Successfully",
        ]);
    }

    //....................................
    public function test_soft_delete_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->deleteJson('api/courses/4');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Delete Course Successfully",
        ]);
    }

    //................................. force delete , restore and trashed courses .....................

    public function test_force_delete_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->deleteJson('api/courses/5/forcedelete');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Force Delete Course Successfully",
        ]);
    }
    //....................................

    public function test_restore_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getJson('api/courses/4/restore');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Restore Course Successfully",
        ]);
    }
    //..................................
    public function test_trashed_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getJson('api/courses-trashed');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get All Trashed Couses Successfully",
        ]);
    }

    //...........................End Crud......................................

       public function test_update_status_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->putJson('api/courses/4/updatestatus',[
            'status'   => 'Closed'
        ]);
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Status Successfully",
        ]);
    }

    //......................................

    public function test_update_start_date_course(): void
    {
        $admin = Teacher::find(1);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson('api/courses/7/updateStartDate',[
            'start_date'   => '2025-01-25'
        ]);

        $course = Course::find(7);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start Date Successfully",
            'data' => [
        "Id"                    =>  $course->id,
        "Title"                 =>  $course->title,
        "Description"           =>  $course->description,
        "Start_register_date"   => $course->start_register_date,
        "End_register_date"     =>  $course->end_register_date,
        "Course Duration"       =>  $course->course_duration,
        "Start_date"            =>  $course->start_date,
        "End_date"              =>  $course->end_date,
        "Status"                =>  $course->status,
        "Teacher"               =>  $course->teacher->name,
        "Category"              =>  $course->category->name
            ]
        ]);
    }

    //..........................................
    public function test_update_end_date_course(): void
    {
        $admin = Teacher::find(1);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson('api/courses/7/updateEndDate',[
            'end_date'   => '2025-08-30'
        ]);

        $course = Course::find(7);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start and End Date Successfully",
            'data' => [
        "Id"                    =>  $course->id,
        "Title"                 =>  $course->title,
        "Description"           =>  $course->description,
        "Start_register_date"   => $course->start_register_date,
        "End_register_date"     =>  $course->end_register_date,
        "Course Duration"       =>  $course->course_duration,
        "Start_date"            =>  $course->start_date,
        "End_date"              =>  $course->end_date,
        "Status"                =>  $course->status,
        "Teacher"               =>  $course->teacher->name,
        "Category"              =>  $course->category->name
            ]
        ]);
    }

    //...........................
    public function test_update_start_register_date_course(): void
    {
        $admin = Teacher::find(1);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson('api/courses/7/updateStartRegisterDate',[
            'start_register_date'   => '2025-08-30'
        ]);

        $course = Course::find(7);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start Register Date Successfully",
            'data' => [
        "Id"                    =>  $course->id,
        "Title"                 =>  $course->title,
        "Description"           =>  $course->description,
        "Start_register_date"   => $course->start_register_date,
        "End_register_date"     =>  $course->end_register_date,
        "Course Duration"       =>  $course->course_duration,
        "Start_date"            =>  $course->start_date,
        "End_date"              =>  $course->end_date,
        "Status"                =>  $course->status,
        "Teacher"               =>  $course->teacher->name,
        "Category"              =>  $course->category->name
            ]
        ]);
    }

    //..................................................
    public function test_update_end_register_date_course(): void
    {
        $admin = Teacher::find(1);
        
        $response = $this->actingAs($admin,'teacher-api')->putJson('api/courses/7/updateEndRegisterDate',[
            'end_register_date'   => '2025-09-01'
        ]);

        $course = Course::find(7);

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Update Start Register Date Successfully",
            'data' => [
        "Id"                    =>  $course->id,
        "Title"                 =>  $course->title,
        "Description"           =>  $course->description,
        "Start_register_date"   => $course->start_register_date,
        "End_register_date"     =>  $course->end_register_date,
        "Course Duration"       =>  $course->course_duration,
        "Start_date"            =>  $course->start_date,
        "End_date"              =>  $course->end_date,
        "Status"                =>  $course->status,
        "Teacher"               =>  $course->teacher->name,
        "Category"              =>  $course->category->name
            ]
        ]);
    }

    //..................................................
    public function test_add_user_to_course(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->postjson('api/courses/7/addUser',[
            'user' => 1
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }
}

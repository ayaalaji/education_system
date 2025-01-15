<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * Test of Crud User
     */
    public function test_index_User(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getjson('api/users');

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get users list successfully",
        ]);
    }

    //................................

    public function test_store_User(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->postJson('api/user',[
            'name'           => 'test1',
            'email'          => 'thest1@gmail.com',
            'password' => Hash::make('password123'),
       
        ]);
        
        $response->assertStatus(201)->assertJsonFragment([
            "status"=> "success",
            "message"=> "User Created Successfully",
        ]);
    }

    //.................................

    public function test_show_User(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getJson('api/users/5');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get user successfully",
        ]);
    }

    //....................................
    public function test_update_User(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->putJson('api/users/6',[
            'password'     => Hash::make('password123'),
        ]);
        
        $response->assertStatus(201)->assertJsonFragment([
            "status"  => "success",
            "message" => "User Updated Successfully",
        ]);
    }

    //....................................
    public function test_soft_delete_User(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->deleteJson('api/users/5');
        
        $response->assertStatus(201)->assertJsonFragment([
            "status"  => "success",
            "message" => "User Deleted Successfully",
        ]);
    }

    //................................. force delete , restore and trashed courses .....................

    public function test_force_delete_user(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->deleteJson('api/users/6/forcedelete');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Force Deleted User Successfully",
        ]);
    }
    //....................................

    public function test_restore_user(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getJson('api/users/5/restore');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Restore User Successfully",
        ]);
    }
    //..................................

    public function test_trashed_user(): void
    {
        $admin = Teacher::find(1);

        $response = $this->actingAs($admin,'teacher-api')->getJson('api/users-getallTrashed');
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get All Trashed Users Successfully",
        ]);
    }
}
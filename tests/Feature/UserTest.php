<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;
use Nette\Utils\Random;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test of Crud User
     */
   
     protected function setUp(): void
     {
         parent::setUp();
 
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
 
         $this->artisan('db:seed');
 
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');
 
     } 

    public function test_index_User(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $response = $this->actingAs($admin,'teacher-api')->getjson('api/users');

        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get users list successfully",
        ]);
    }

    //................................

    public function test_store_User(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
  
        $response = $this->actingAs($admin,'teacher-api')->postJson('api/users',[
            'name'           => 'test1',
            'email'          => Random::generate(5).'@gmail.com',
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
        $admin = Teacher::where('email', 'admin@gmail.com')->first();
     
        $user = User::factory()->create();

        $response = $this->actingAs($admin,'teacher-api')->getJson("api/users/{$user->id}");
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get user successfully",
        ]);
    }

    //....................................
    public function test_update_User(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $user = User::factory()->create();

        $response = $this->actingAs($admin,'teacher-api')->putJson("api/users/{$user->id}",[
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
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $user = User::factory()->create();
        
        $response = $this->actingAs($admin,'teacher-api')->deleteJson("api/users/{$user->id}");
        
        $response->assertStatus(201)->assertJsonFragment([
            "status"  => "success",
            "message" => "User Deleted Successfully",
        ]);
    }

    //................................. force delete , restore and trashed courses .....................

    public function test_force_delete_user(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $user = User::factory()->create();

        $user->delete();

        $response = $this->actingAs($admin,'teacher-api')->deleteJson("api/users/{$user->id}/forcedelete");
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Force Deleted User Successfully",
        ]);
    }
    //....................................

    public function test_restore_user(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $user = User::factory()->create();

        $user->delete();

        $response = $this->actingAs($admin,'teacher-api')->getJson("api/users/{$user->id}/restore");
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Restore User Successfully",
        ]);
    }
    //..................................

    public function test_trashed_user(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $user = User::factory()->create();

        $user->delete();

        $response = $this->actingAs($admin,'teacher-api')->getJson("api/users/trashed");
        
        $response->assertStatus(200)->assertJsonFragment([
            "status"  => "success",
            "message" => "Get All Trashed Users Successfully",
        ]);
    }
}
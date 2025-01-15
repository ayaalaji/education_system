<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
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
     * Test the method index
     */
    public function test_get_all_roles(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/roles');

        $response->assertStatus(200);
    }

    /**
     * Test the method store (create a new role).
     */
    public function test_create_role(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $token = JWTAuth::fromUser($admin);

        $permissions = Permission::whereIn('name', ['add_role', 'show_role', 'delete_role'])
        ->pluck('id')
        ->toArray();

        $data = [
            'name' => 'visitor',
            'permission' => $permissions,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', $data);

        $response->assertStatus(201);
    }
   /**
    * Test the method show (retrieve a specific role by ID).
    */
    public function test_show_specific_role(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $token = JWTAuth::fromUser($admin);

        //create role to test it
        $permissions = Permission::whereIn('name', ['add_role', 'show_role', 'delete_role'])
            ->pluck('id')
            ->toArray();

        $data = [
            'name' => 'visitor',
            'permission' => $permissions,
        ];

        $createdRoleResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', $data);

        $createdRoleResponse->assertStatus(201);

        //Extract the created role ID
        $roleId = $createdRoleResponse->json('data.id');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/roles/{$roleId}");

        $response->assertStatus(200);

    }
   /**
    * Test the method show (retrieve a specific role by ID).
    */
    public function test_update_specific_role(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $token = JWTAuth::fromUser($admin);

        //create role to test it
        $permissions = Permission::whereIn('name', ['add_role', 'show_role', 'delete_role'])
            ->pluck('id')
            ->toArray();

        $data = [
            'name' => 'visitor',
            'permission' => $permissions,
        ];

        $createdRoleResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', $data);

        $createdRoleResponse->assertStatus(201);

        //Extract the created role ID
        $roleId = $createdRoleResponse->json('data.id');

        $updatedData = [
            'name' => 'visitorss',
            'permission' => $permissions,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/roles/{$roleId}", $updatedData);

        $response->assertStatus(200);

    }
    /**
    * Test the method show (retrieve a specific role by ID).
    */
    public function test_delete_specific_role(): void
    {
        $admin = Teacher::where('email', 'admin@gmail.com')->first();

        $token = JWTAuth::fromUser($admin);

        //create role to test it
        $permissions = Permission::whereIn('name', ['add_role', 'show_role', 'delete_role'])
            ->pluck('id')
            ->toArray();

        $data = [
            'name' => 'visitor',
            'permission' => $permissions,
        ];

        $createdRoleResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', $data);

        $createdRoleResponse->assertStatus(201);

        //Extract the created role ID
        $roleId = $createdRoleResponse->json('data.id');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/roles/{$roleId}");

        $response->assertStatus(200);

    }


}

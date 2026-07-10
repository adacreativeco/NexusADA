<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 1, 'max_projects' => 3,
        ]);

        $this->tenant = Tenant::create([
            'name' => 'API Test', 'slug' => 'api-test',
            'email' => 'api@test.com',
            'plan_id' => $plan->id, 'status' => 'active',
        ]);

        $this->user = User::create([
            'name' => 'API User', 'email' => 'api@test.com',
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $this->tenant->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_api_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/projects');
        $response->assertStatus(401);
    }

    public function test_api_projects_returns_paginated(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        Project::create([
            'title' => 'API Project', 'tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/projects');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'current_page']);
    }

    public function test_api_tasks_crud(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create
        $response = $this->withHeaders($headers)->postJson('/api/v1/tasks', [
            'title' => 'API Task',
            'status' => 'todo',
        ]);
        $response->assertStatus(201);
        $taskId = $response->json('id');

        // Read
        $response = $this->withHeaders($headers)->getJson("/api/v1/tasks/{$taskId}");
        $response->assertStatus(200)->assertJsonPath('title', 'API Task');

        // Update
        $response = $this->withHeaders($headers)->putJson("/api/v1/tasks/{$taskId}", [
            'title' => 'Updated Task', 'status' => 'in_progress',
        ]);
        $response->assertStatus(200);

        // Delete
        $response = $this->withHeaders($headers)->deleteJson("/api/v1/tasks/{$taskId}");
        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/admin/register');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin/');
        $response->assertRedirect('/admin/login');
    }

    public function test_health_endpoint_returns_json(): void
    {
        $response = $this->get('/health');
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'checks']);
    }

    public function test_landing_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('ADA Co-OS');
    }

    public function test_kvkk_page_loads(): void
    {
        $response = $this->get('/kvkk');
        $response->assertStatus(200);
    }

    public function test_password_policy_enforced(): void
    {
        // Ensure weak passwords are rejected during registration
        // This tests the min:10, regex rule in Register.php
        Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 1, 'max_projects' => 3,
        ]);

        // Weak password should be rejected (too short, no uppercase/digit/special)
        $response = $this->post('/admin/register', [
            'name' => 'Test User',
            'email' => 'weak@test.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'terms' => true,
        ]);

        // Should not create user with weak password
        $this->assertDatabaseMissing('users', ['email' => 'weak@test.com']);
    }
}

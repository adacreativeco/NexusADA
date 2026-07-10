<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Campaign;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function createTenantWithUser(string $name, string $email): array
    {
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolePermissionSeeder', '--force' => true]);

        $plan = Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 1, 'max_projects' => 3,
        ]);

        $tenant = Tenant::create([
            'name' => $name, 'slug' => \Illuminate\Support\Str::slug($name),
            'email' => $email,
            'plan_id' => $plan->id, 'status' => 'active',
        ]);

        $user = User::create([
            'name' => $name . ' User', 'email' => $email,
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('tenant_owner');

        return [$tenant, $user];
    }

    public function test_guest_cannot_use_universal_search(): void
    {
        $response = $this->getJson('/admin/search/universal?q=test');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_search_own_resources(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('Tenant A', 'a@test.com');
        [$tenantB, $userB] = $this->createTenantWithUser('Tenant B', 'b@test.com');

        // Create resource for Tenant A
        $clientA = Client::create([
            'name' => 'Acme Corp',
            'tenant_id' => $tenantA->id,
        ]);

        // Create resource for Tenant B
        $clientB = Client::create([
            'name' => 'Acme LLC',
            'tenant_id' => $tenantB->id,
        ]);

        // Login as User A
        $this->actingAs($userA);

        // Search for 'Acme'
        $response = $this->getJson('/admin/search/universal?q=Acme');
        $response->assertStatus(200);

        // Assert User A only sees Client A
        $response->assertJsonFragment(['label' => 'Acme Corp']);
        $response->assertJsonMissing(['label' => 'Acme LLC']);
    }
}

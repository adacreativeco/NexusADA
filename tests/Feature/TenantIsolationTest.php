<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
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

    public function test_user_cannot_access_other_tenant_projects(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('Firma A', 'a@test.com');
        [$tenantB, $userB] = $this->createTenantWithUser('Firma B', 'b@test.com');

        // Create project for tenant B
        $project = Project::create([
            'client_name' => 'Test Project', 'tenant_id' => $tenantB->id,
            'status' => 'active',
        ]);

        // Login as user A
        $this->actingAs($userA);

        // User A should NOT see tenant B's projects due to BelongsToTenant scope
        $visibleProjects = Project::all();
        $this->assertFalse($visibleProjects->contains('id', $project->id));
    }

    public function test_user_can_see_own_tenant_projects(): void
    {
        [$tenant, $user] = $this->createTenantWithUser('My Firm', 'me@test.com');

        $project = Project::create([
            'client_name' => 'My Project', 'tenant_id' => $tenant->id,
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $visibleProjects = Project::all();
        $this->assertTrue($visibleProjects->contains('id', $project->id));
    }
}

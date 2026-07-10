<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Task;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
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

    public function test_project_creation_logs_activity(): void
    {
        [$tenant, $user] = $this->createTenantWithUser('Tenant A', 'a@test.com');
        $this->actingAs($user);

        // Create project
        $project = Project::create([
            'client_name' => 'Acme Corp',
            'tenant_id' => $tenant->id,
            'status' => 'active',
        ]);

        // Assert activity was logged
        $activity = Activity::where('model_type', Project::class)
            ->where('model_id', $project->id)
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('user', $activity->activity_type);
        $this->assertEquals($tenant->id, $activity->tenant_id);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure test plan exists
        $plan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'is_active' => true,
                'max_users' => 10,
                'max_projects' => 100,
            ]
        );

        // Ensure test tenant exists
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'test-agency'],
            [
                'name' => 'Test Agency',
                'email' => 'test@agency.com',
                'plan_id' => $plan->id,
                'status' => 'active',
            ]
        );

        // Ensure roles exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'webmaster', 'guard_name' => 'web']);

        // Create or find a webmaster user
        $this->user = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]
        );
        $this->user->assignRole('webmaster');
    }

    #[Test]
    public function admin_dashboard_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin');
        $response->assertStatus(200);
    }

    #[Test]
    public function projects_listing_loads_successfully()
    {
        // Add a project to ensure listing logic (and any ghost columns) is hit
        \App\Models\Project::create([
            'title' => 'Test Project',
            'tenant_id' => $this->user->tenant_id,
        ]);

        $response = $this->actingAs($this->user)->get('/admin/projects');
        $response->assertStatus(200);
    }

    #[Test]
    public function timesheet_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/timesheet');
        $response->assertStatus(200);
    }

    #[Test]
    public function task_board_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/tasks/board');
        $response->assertStatus(200);
    }

    #[Test]
    public function gantt_chart_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/gantt');
        $response->assertStatus(200);
    }

    #[Test]
    public function works_listing_loads_successfully()
    {
        $work = \App\Models\Work::create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Test Work 1',
            'status' => 'lead',
        ]);

        $response = $this->actingAs($this->user)->get('/admin/works');
        $response->assertStatus(200);
    }

    #[Test]
    public function works_pipeline_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/works/pipeline');
        $response->assertStatus(200);
    }

    #[Test]
    public function works_timeline_loads_successfully()
    {
        $work = \App\Models\Work::create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Test Work 2',
            'status' => 'proposal',
        ]);

        $response = $this->actingAs($this->user)->get("/admin/works/{$work->id}/timeline");
        $response->assertStatus(200);
    }

    #[Test]
    public function proposals_listing_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/proposals');
        $response->assertStatus(200);
    }

    #[Test]
    public function contracts_listing_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/contracts');
        $response->assertStatus(200);
    }

    #[Test]
    public function proposal_builder_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/proposal');
        $response->assertStatus(200);
    }

    #[Test]
    public function notifications_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/admin/notifications');
        $response->assertStatus(200);
    }
}

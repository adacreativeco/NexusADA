<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Workflow;
use App\Models\Work;
use App\Models\Client;
use App\Models\Task;
use App\Services\WorkflowEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'is_active' => true,
                'max_users' => 10,
                'max_projects' => 100,
            ]
        );

        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'test-agency'],
            [
                'name' => 'Test Agency',
                'email' => 'test@agency.com',
                'plan_id' => $plan->id,
                'status' => 'active',
            ]
        );

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'webmaster', 'guard_name' => 'web']);

        $this->user = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'tenant_id' => $this->tenant->id,
                'email_verified_at' => now(),
            ]
        );
        $this->user->assignRole('webmaster');
    }

    public function test_workflow_designer_page_loads(): void
    {
        $this->actingAs($this->user);

        $workflow = Workflow::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tasarım Akışı',
            'steps' => [
                ['label' => 'İlk Görüşme', 'role' => 'pm', 'action' => 'create_task']
            ],
            'is_active' => true,
        ]);

        $response = $this->get('/admin/workflows/' . $workflow->id . '/design');
        $response->assertStatus(200);
    }

    public function test_workflow_runs_and_creates_tasks_on_completion_chain(): void
    {
        $this->actingAs($this->user);

        $workflow = Workflow::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tasarım Akışı',
            'steps' => [
                ['label' => 'Adım 1', 'role' => 'pm', 'action' => 'create_task'],
                ['label' => 'Adım 2', 'role' => 'designer', 'action' => 'create_task']
            ],
            'is_active' => true,
        ]);

        $client = Client::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Müşteri 1',
        ]);

        $work = Work::create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $client->id,
            'title' => 'Tasarım İşi',
            'status' => 'lead',
        ]);

        // Start workflow
        $activeWorkflow = WorkflowEngine::start($work, $workflow);

        $this->assertEquals(0, $activeWorkflow->current_step_index);
        $this->assertEquals('running', $activeWorkflow->status);

        // Verify task for Step 1 was created
        $task1 = Task::where('work_id', $work->id)->where('title', 'Adım 1')->first();
        $this->assertNotNull($task1);

        // Complete task 1
        $task1->update(['status' => 'done']);

        // Check if workflow advanced to Step 2
        $activeWorkflow->refresh();
        $this->assertEquals(1, $activeWorkflow->current_step_index);

        // Verify task for Step 2 was created
        $task2 = Task::where('work_id', $work->id)->where('title', 'Adım 2')->first();
        $this->assertNotNull($task2);
    }
}

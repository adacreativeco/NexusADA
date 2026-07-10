<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientPortalSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected ClientUser $clientUser;
    protected Client $client;
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

        $this->client = Client::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Acme Corp',
        ]);

        $this->clientUser = ClientUser::create([
            'client_id' => $this->client->id,
            'name' => 'Müşteri Kullanıcısı',
            'email' => 'client@acme.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_client_login_page_loads_successfully(): void
    {
        $response = $this->get('/client/login');
        $response->assertStatus(200);
    }

    public function test_authenticated_client_dashboard_loads_with_context(): void
    {
        // Seed projects & incomes
        Project::create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'title' => 'Web Design Project',
            'status' => 'active',
        ]);

        \App\Models\Income::create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'income_number' => 'INV-2026-0001',
            'title' => 'Tasarım Hizmet Bedeli',
            'amount' => 15000.00,
            'tax_rate' => 20,
            'grand_total' => 18000.00,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->clientUser, 'client')
            ->get('/client');

        $response->assertStatus(200);
        $response->assertSee('INV-2026-0001');

        $projectResponse = $this->actingAs($this->clientUser, 'client')
            ->get('/client/projects');

        $projectResponse->assertStatus(200);
        $projectResponse->assertSee('Web Design Project');
    }

    public function test_pwa_manifest_and_service_worker_render_successfully(): void
    {
        $response1 = $this->get('/manifest.json');
        $response1->assertStatus(200);
        $response1->assertHeader('Content-Type', 'application/json');

        $response2 = $this->get('/sw.js');
        $response2->assertStatus(200);
    }
}

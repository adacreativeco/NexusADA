<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\AIMemory;
use App\Models\AiUsageLog;
use App\Services\AIService;
use App\Services\DailyBriefingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AISmokeTest extends TestCase
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

    public function test_ai_memories_nexus_table_page_loads_successfully(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin/ai-memories');
        $response->assertStatus(200);
    }

    public function test_ai_service_returns_fallback_mock_response_when_no_api_key(): void
    {
        $this->actingAs($this->user);

        // Under tests, it will fall back to mock automatically
        $reply = AIService::ask('Analyze this client', '', 'client_analyze', $this->tenant->id);

        $this->assertStringContainsString('Müşteri Durum Analizi', $reply);
        $this->assertStringContainsString('ADA AI', $reply);
    }

    public function test_daily_briefing_compiles_data_and_returns_summary(): void
    {
        $this->actingAs($this->user);

        // Seed a sample AI memory
        AIMemory::create([
            'tenant_id' => $this->tenant->id,
            'category' => 'style',
            'content' => 'Always welcome users warmly.',
            'source' => 'admin_set',
            'is_active' => true,
        ]);

        $briefing = DailyBriefingService::getBriefing($this->tenant->id);

        $this->assertNotEmpty($briefing);
        $this->assertStringContainsString('Güne başlarken', $briefing);
        $this->assertStringContainsString('Banka hesaplarımızda', $briefing);
    }
}

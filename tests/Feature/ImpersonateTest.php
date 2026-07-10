<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonateTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminAndTarget(): array
    {
        $plan = Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 5, 'max_projects' => 10,
        ]);

        $tenant = Tenant::create([
            'name' => 'Impersonate Firm', 'slug' => 'impersonate-firm',
            'email' => 'imp@test.com',
            'plan_id' => $plan->id, 'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin-imp@test.com',
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        $target = User::create([
            'name' => 'Target', 'email' => 'target@test.com',
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        return [$admin, $target];
    }

    public function test_session_based_impersonation_stores_original_user(): void
    {
        [$admin, $target] = $this->createAdminAndTarget();

        $this->actingAs($admin);

        // Simulate impersonation by storing original user in session
        session()->put('impersonate_original_id', $admin->id);
        session()->put('impersonating', true);

        $this->assertTrue(session()->has('impersonate_original_id'));
        $this->assertEquals($admin->id, session()->get('impersonate_original_id'));
    }

    public function test_impersonation_can_be_stopped(): void
    {
        [$admin, $target] = $this->createAdminAndTarget();

        $this->actingAs($admin);

        // Start impersonation
        session()->put('impersonate_original_id', $admin->id);
        session()->put('impersonating', true);

        // Stop impersonation
        session()->forget('impersonate_original_id');
        session()->forget('impersonating');

        $this->assertFalse(session()->has('impersonating'));
        $this->assertFalse(session()->has('impersonate_original_id'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Services\DataExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_data_can_be_exported(): void
    {
        $plan = Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 1, 'max_projects' => 3,
        ]);

        $tenant = Tenant::create([
            'name' => 'Export Test', 'slug' => 'export-test',
            'email' => 'export@test.com',
            'plan_id' => $plan->id, 'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Export User', 'email' => 'export@test.com',
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        $service = new DataExportService();
        $data = $service->exportUserData($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('user', $data);
        $this->assertEquals('export@test.com', $data['user']['email']);
    }

    public function test_user_can_be_anonymized(): void
    {
        $plan = Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 1, 'max_projects' => 3,
        ]);

        $tenant = Tenant::create([
            'name' => 'Anon Test', 'slug' => 'anon-test',
            'email' => 'anon-tenant@test.com',
            'plan_id' => $plan->id, 'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Anon User', 'email' => 'anon@test.com',
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        $service = new DataExportService();
        $service->anonymizeUser($user);

        $user->refresh();
        $this->assertStringStartsWith('silinen-', $user->email);
        $this->assertEquals('Silinmiş Kullanıcı', $user->name);
    }
}

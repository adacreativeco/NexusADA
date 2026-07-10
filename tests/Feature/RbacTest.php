<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function createTenantUser(string $role = 'tenant_owner'): User
    {
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolePermissionSeeder', '--force' => true]);

        $plan = Plan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'monthly_price' => 0, 'yearly_price' => 0,
            'max_users' => 5, 'max_projects' => 10,
        ]);

        $tenant = Tenant::create([
            'name' => 'RBAC Test Firm', 'slug' => 'rbac-test-' . uniqid(),
            'email' => 'rbac@test.com',
            'plan_id' => $plan->id, 'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'RBAC User', 'email' => 'rbac-' . uniqid() . '@test.com',
            'password' => bcrypt('Test1234!@'), 'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        if (Role::where('name', $role)->exists()) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function test_admin_panel_requires_authentication(): void
    {
        $response = $this->get('/admin/');
        $response->assertRedirect('/admin/login');
    }

    public function test_roles_exist_in_database(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolePermissionSeeder', '--force' => true]);

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'tenant_owner']);
    }

    public function test_permissions_exist_in_database(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolePermissionSeeder', '--force' => true]);

        $permissionCount = Permission::count();
        $this->assertGreaterThan(0, $permissionCount);
    }
}

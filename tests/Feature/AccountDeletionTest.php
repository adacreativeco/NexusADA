<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_user_can_delete_account()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'email' => 'test@tenant.com',
            'status' => 'active',
            'max_projects' => 10,
            'max_users' => 10,
        ]);
        
        $owner = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'owner@test.com'
        ]);
        
        // Ensure time difference for oldest() in MySQL
        sleep(1);
        
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'user@test.com'
        ]);

        $this->actingAs($user);
        
        $response = $this->delete(route('admin.account.destroy'), [
            'password' => 'password',
            'confirmation' => 'CONFIRM',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $response->assertRedirect(route('admin.login'));
        
        $deletedUser = User::withTrashed()->find($user->id);
        $this->assertEquals('[Silinmiş Kullanıcı]', $deletedUser->name);
        $this->assertStringStartsWith('deleted_', $deletedUser->email);
    }

    public function test_owner_cannot_delete_account()
    {
        $tenant = Tenant::create([
            'name' => 'Owner Tenant',
            'slug' => 'owner-tenant',
            'email' => 'owner@tenant.com',
            'status' => 'active',
            'max_projects' => 5,
            'max_users' => 5,
        ]);
        
        $owner = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'owner2@test.com'
        ]);
        
        $this->actingAs($owner);

        $response = $this->delete(route('admin.account.destroy'), [
            'password' => 'password',
            'confirmation' => 'CONFIRM',
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'email' => 'owner2@test.com']);
    }
}

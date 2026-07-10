<?php

namespace Tests\Feature;

use App\Models\EmailAccount;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\EmailAccountManager;
use Illuminate\Support\Facades\Hash;

class ImapSyncTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'slug' => 'test-tenant',
            'email' => 'admin@test-tenant.com',
        ]);
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_email_account_password_is_encrypted()
    {
        $account = EmailAccount::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'email' => 'test@example.com',
            'imap_host' => 'imap.example.com',
            'username' => 'testuser',
            'password' => 'secret_password',
            'folder' => 'INBOX',
        ]);

        $this->assertDatabaseHas('email_accounts', [
            'email' => 'test@example.com',
        ]);

        $rawDbPassword = \DB::table('email_accounts')->where('id', $account->id)->value('password');
        $this->assertNotEquals('secret_password', $rawDbPassword);
        $this->assertEquals('secret_password', $account->password);
    }

    public function test_livewire_component_can_render_and_save()
    {
        Livewire::actingAs($this->user)
            ->test(EmailAccountManager::class)
            ->assertSee('E-posta')
            ->set('email', 'livewire@example.com')
            ->set('imap_host', 'imap.livewire.com')
            ->set('imap_port', 993)
            ->set('imap_encryption', 'ssl')
            ->set('username', 'livewire_user')
            ->set('password', 'livewire_pass')
            ->set('folder', 'INBOX')
            ->set('sync_interval_minutes', 5)
            ->call('save')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('email_accounts', [
            'email' => 'livewire@example.com',
        ]);
    }
}

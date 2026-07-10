<?php

namespace App\Livewire\Admin;

use App\Models\EmailAccount;
use App\Services\ImapSyncService;
use Livewire\Component;

class EmailAccountManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields
    public string $email = '';
    public string $imap_host = '';
    public int $imap_port = 993;
    public string $imap_encryption = 'ssl';
    public string $username = '';
    public string $password = '';
    public string $folder = 'INBOX';
    public int $sync_interval_minutes = 5;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'imap_host' => 'required|string',
            'imap_port' => 'required|integer|min:1|max:65535',
            'imap_encryption' => 'required|in:ssl,tls,none',
            'username' => 'required|string',
            'password' => $this->editingId ? 'nullable|string' : 'required|string',
            'folder' => 'required|string',
            'sync_interval_minutes' => 'required|integer|min:1|max:60',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $account = EmailAccount::findOrFail($id);
        $this->editingId = $id;
        $this->email = $account->email;
        $this->imap_host = $account->imap_host;
        $this->imap_port = $account->imap_port;
        $this->imap_encryption = $account->imap_encryption;
        $this->username = $account->username;
        $this->password = ''; // Never show existing password
        $this->folder = $account->folder;
        $this->sync_interval_minutes = $account->sync_interval_minutes;
        $this->is_active = $account->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'email' => $this->email,
            'imap_host' => $this->imap_host,
            'imap_port' => $this->imap_port,
            'imap_encryption' => $this->imap_encryption,
            'username' => $this->username,
            'folder' => $this->folder,
            'sync_interval_minutes' => $this->sync_interval_minutes,
            'is_active' => $this->is_active,
            'tenant_id' => auth()->user()->tenant_id ?? session('impersonating_tenant_id'),
            'user_id' => auth()->id(),
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->editingId) {
            EmailAccount::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'E-posta hesabı güncellendi.', type: 'success');
        } else {
            EmailAccount::create($data);
            $this->dispatch('notify', message: 'E-posta hesabı eklendi.', type: 'success');
        }

        $this->closeForm();
    }

    public function testConnection(int $id): void
    {
        $account = EmailAccount::findOrFail($id);
        $service = app(ImapSyncService::class);

        try {
            $result = $service->testConnection($account);
            if ($result['success']) {
                $this->dispatch('notify', message: "Bağlantı başarılı! {$result['message_count']} mesaj bulundu.", type: 'success');
            } else {
                $this->dispatch('notify', message: "Bağlantı hatası: {$result['error']}", type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Bağlantı hatası: ' . $e->getMessage(), type: 'error');
        }
    }

    public function syncNow(int $id): void
    {
        $account = EmailAccount::findOrFail($id);
        $service = app(ImapSyncService::class);

        try {
            $count = $service->syncAccount($account);
            $this->dispatch('notify', message: "{$count} yeni e-posta senkronize edildi.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Senkronizasyon hatası: ' . $e->getMessage(), type: 'error');
        }
    }

    public function toggleActive(int $id): void
    {
        $account = EmailAccount::findOrFail($id);
        $account->update(['is_active' => !$account->is_active]);
        $this->dispatch('notify',
            message: $account->is_active ? 'Hesap aktifleştirildi.' : 'Hesap devre dışı bırakıldı.',
            type: 'info'
        );
    }

    public function delete(int $id): void
    {
        EmailAccount::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'E-posta hesabı silindi.', type: 'success');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->email = '';
        $this->imap_host = '';
        $this->imap_port = 993;
        $this->imap_encryption = 'ssl';
        $this->username = '';
        $this->password = '';
        $this->folder = 'INBOX';
        $this->sync_interval_minutes = 5;
        $this->is_active = true;
    }

    public function render()
    {
        $accounts = EmailAccount::latest()->get();

        return view('livewire.admin.email-account-manager', [
            'accounts' => $accounts,
        ])->layout('layouts.admin', [
            'title' => 'E-posta Hesapları',
            'breadcrumb' => 'E-posta Hesapları',
        ]);
    }
}

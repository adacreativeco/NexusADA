<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class TeamManager extends Component
{
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ROLE HIERARCHY — Three layers
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    const PLATFORM_ROLES = [
        'super_admin', 'admin', 'moderator', 'editor', 'contributor',
        'viewer', 'guest', 'billing_admin', 'auditor',
        'support_agent', 'developer', 'api_user',
    ];

    const TENANT_ROLES = [
        'tenant_owner', 'tenant_admin', 'tenant_billing',
        'tenant_member', 'tenant_viewer', 'tenant_guest',
    ];

    const WORKSPACE_ROLES = [
        'workspace_admin', 'workspace_member', 'workspace_viewer',
    ];

    // Hierarchy order (lower index = higher rank)
    // tenant_owner can assign everything below them
    // tenant_admin can assign everything below them (NOT tenant_owner)
    const TENANT_HIERARCHY = [
        'tenant_owner', 'tenant_admin', 'tenant_billing',
        'tenant_member', 'tenant_viewer', 'tenant_guest',
    ];

    const ROLE_LABELS = [
        // Platform
        'super_admin'   => 'Super Admin — Tam sistem yetkisi, ayarlar + faturalama',
        'admin'         => 'Yönetici — Organizasyon bazında tam yetki, kullanıcı yönetimi',
        'moderator'     => 'Moderatör — İçerik + kullanıcı yönetimi, sistem ayarları hariç',
        'editor'        => 'Editör — İçerik oluşturur, düzenler ve yayınlar',
        'contributor'   => 'Katkı Sağlayıcı — İçerik oluşturur, yayınlama onayı gerekir',
        'viewer'        => 'Görüntüleyici — Salt okunur, hiçbir şeyi değiştiremez',
        'guest'         => 'Misafir — Çok kısıtlı, geçici erişim',
        'billing_admin' => 'Fatura Yöneticisi — Sadece ödeme ve fatura yönetimi',
        'auditor'       => 'Denetçi — Her şeyi görebilir, hiçbir şey değiştiremez',
        'support_agent' => 'Destek Uzmanı — Kullanıcı sorunları için kısıtlı yetki',
        'developer'     => 'Geliştirici — API anahtarları, webhook, teknik ayarlar',
        'api_user'      => 'API Kullanıcısı — Makine-makine entegrasyon erişimi',
        // Tenant
        'tenant_owner'   => 'Tenant Sahibi — Tam ajans yetkisi, hesabı silebilir',
        'tenant_admin'   => 'Ajans Admin — Kullanıcı yönetimi + ayarlar, hesap silme hariç',
        'tenant_billing' => 'Ajans Fatura — Sadece fatura/abonelik yönetimi',
        'tenant_member'  => 'Üye — Standart kullanıcı, içerik oluşturur',
        'tenant_viewer'  => 'Görüntüleyici — Salt okunur',
        'tenant_guest'   => 'Misafir — Davet ile sınırlı erişim',
        // Workspace
        'workspace_admin'  => 'Çalışma Alanı Admin — Sadece o workspace\'i yönetir',
        'workspace_member' => 'Çalışma Alanı Üye — O workspace\'de çalışır',
        'workspace_viewer' => 'Çalışma Alanı Görüntüleyici — Sadece görüntüler',
    ];

    const ROLE_BADGE_LABELS = [
        'super_admin'      => 'Super Admin',
        'admin'            => 'Yönetici',
        'moderator'        => 'Moderatör',
        'editor'           => 'Editör',
        'contributor'      => 'Katkı Sağlayıcı',
        'viewer'           => 'Görüntüleyici',
        'guest'            => 'Misafir',
        'billing_admin'    => 'Fatura Yön.',
        'auditor'          => 'Denetçi',
        'support_agent'    => 'Destek',
        'developer'        => 'Geliştirici',
        'api_user'         => 'API',
        'tenant_owner'     => 'Tenant Sahibi',
        'tenant_admin'     => 'Ajans Admin',
        'tenant_billing'   => 'Ajans Fatura',
        'tenant_member'    => 'Üye',
        'tenant_viewer'    => 'Görüntüleyici',
        'tenant_guest'     => 'Misafir',
        'workspace_admin'  => 'WS Admin',
        'workspace_member' => 'WS Üye',
        'workspace_viewer' => 'WS Görüntüleyici',
    ];

    const ROLE_BADGE_COLORS = [
        'super_admin'      => 'success',
        'admin'            => 'danger',
        'moderator'        => 'warning',
        'editor'           => 'info',
        'contributor'      => 'info',
        'viewer'           => 'gray',
        'guest'            => 'gray',
        'billing_admin'    => 'warning',
        'auditor'          => 'gray',
        'support_agent'    => 'info',
        'developer'        => 'success',
        'api_user'         => 'gray',
        'tenant_owner'     => 'success',
        'tenant_admin'     => 'danger',
        'tenant_billing'   => 'warning',
        'tenant_member'    => 'info',
        'tenant_viewer'    => 'gray',
        'tenant_guest'     => 'gray',
        'workspace_admin'  => 'danger',
        'workspace_member' => 'info',
        'workspace_viewer' => 'gray',
    ];

    public bool $editorOpen = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $selectedRole = '';
    public bool $isActive = true;
    public ?int $confirmingDeleteId = null;

    protected function rules()
    {
        $emailRule = $this->editingId
            ? 'required|email|unique:users,email,' . $this->editingId
            : 'required|email|unique:users,email';

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'selectedRole' => 'required|exists:roles,name',
        ];
    }

    protected function isPlatformAdmin(): bool
    {
        return auth()->user()->isWebmaster();
    }

    /**
     * Get the current user's tenant role rank (lower = higher authority).
     * Returns null if user has no tenant role.
     */
    protected function getCurrentTenantRank(): ?int
    {
        $user = auth()->user();
        $roleName = $user->roles->first()?->name;

        $rank = array_search($roleName, self::TENANT_HIERARCHY);
        return $rank !== false ? $rank : null;
    }

    /**
     * Get roles available for assignment.
     *
     * Platform admin → platform roles
     * Tenant user → only tenant roles BELOW their own rank
     */
    protected function getAvailableRoles()
    {
        if ($this->isPlatformAdmin()) {
            return Role::where('scope', 'platform')->orderBy('name')->get();
        }

        // Tenant hierarchy enforcement:
        // tenant_owner can assign: tenant_admin, tenant_billing, tenant_member, tenant_viewer, tenant_guest
        // tenant_admin can assign: tenant_billing, tenant_member, tenant_viewer, tenant_guest
        // Others cannot assign roles (they don't have manage_team permission anyway)
        $currentRank = $this->getCurrentTenantRank();
        if ($currentRank === null) {
            return collect();
        }

        $assignableRoles = array_slice(self::TENANT_HIERARCHY, $currentRank + 1);
        return Role::whereIn('name', $assignableRoles)->orderBy('name')->get();
    }

    /**
     * Users scoped to context + isolation rules.
     *
     * - Platform admin + impersonate → hedef kiracının ekibi
     * - Platform admin (normal) → sadece platform ekibi
     * - Tenant kullanıcı → kendi tenant ekibi
     */
    protected function getScopedUsers()
    {
        // Impersonate aktif → hedef kiracının ekibini göster
        $impersonatingTenantId = session('impersonating_tenant_id');
        if ($impersonatingTenantId && $this->isPlatformAdmin()) {
            return User::with('roles')
                ->where('tenant_id', $impersonatingTenantId)
                ->orderBy('name')
                ->get();
        }

        // Platform admin → sadece platform ekibi (tenant_id = NULL)
        if ($this->isPlatformAdmin()) {
            return User::with('roles')
                ->whereNull('tenant_id')
                ->orderBy('name')
                ->get();
        }

        // Tenant kullanıcı → kendi ekibi
        $user = auth()->user();
        if ($user->tenant_id) {
            return User::with('roles')
                ->where('tenant_id', $user->tenant_id)
                ->orderBy('name')
                ->get();
        }

        return User::with('roles')->where('id', $user->id)->get();
    }

    protected function canAssignRole(string $roleName): bool
    {
        if ($this->isPlatformAdmin()) {
            return in_array($roleName, self::PLATFORM_ROLES);
        }

        $currentRank = $this->getCurrentTenantRank();
        if ($currentRank === null) return false;

        $targetRank = array_search($roleName, self::TENANT_HIERARCHY);
        if ($targetRank === false) return false;

        // Can only assign roles LOWER than own rank (higher index)
        return $targetRank > $currentRank;
    }

    public function openEditor(?int $id = null)
    {
        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $user = User::findOrFail($id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->password = '';
            $this->selectedRole = $user->roles->first()?->name ?? '';
            $this->isActive = true;
        } else {
            $this->name = '';
            $this->email = '';
            $this->password = '';
            $this->selectedRole = $this->isPlatformAdmin() ? 'viewer' : 'tenant_viewer';
            $this->isActive = true;
        }

        $this->editorOpen = true;
    }

    public function closeEditor()
    {
        $this->editorOpen = false;
        $this->editingId = null;
        $this->reset(['name', 'email', 'password', 'selectedRole']);
    }

    public function save()
    {
        $this->validate();

        if (!$this->editingId && empty($this->password)) {
            $this->addError('password', 'Yeni kullanıcı için şifre zorunludur.');
            return;
        }

        if (!$this->canAssignRole($this->selectedRole)) {
            $this->addError('selectedRole', 'Bu rolü atama yetkiniz yok.');
            return;
        }

        $currentUser = auth()->user();

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);
            if (!empty($this->password)) {
                $user->update(['password' => Hash::make($this->password)]);
            }
            $this->dispatch('notify', type: 'success', message: 'Kullanıcı güncellendi.');
        } else {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ];

            if ($currentUser->tenant_id) {
                $data['tenant_id'] = $currentUser->tenant_id;
            }

            $user = User::create($data);
            $this->dispatch('notify', type: 'success', message: 'Kullanıcı oluşturuldu.');
        }

        $user->syncRoles([$this->selectedRole]);
        $this->closeEditor();
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteUser(?int $id = null)
    {
        $deleteId = $id ?? $this->confirmingDeleteId;
        if (!$deleteId) return;

        $user = User::findOrFail($deleteId);
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Kendinizi silemezsiniz.');
            $this->confirmingDeleteId = null;
            return;
        }

        // Hierarchy check: can't delete someone with equal or higher rank
        if (!$this->isPlatformAdmin()) {
            $currentRank = $this->getCurrentTenantRank();
            $targetRole = $user->roles->first()?->name;
            $targetRank = array_search($targetRole, self::TENANT_HIERARCHY);
            if ($targetRank !== false && $currentRank !== null && $targetRank <= $currentRank) {
                $this->dispatch('notify', type: 'error', message: 'Kendinizle aynı veya üst seviyedeki bir kullanıcıyı silemezsiniz.');
                $this->confirmingDeleteId = null;
                return;
            }
        }

        $user->delete();
        $this->confirmingDeleteId = null;
        $this->dispatch('notify', type: 'success', message: 'Kullanıcı silindi.');
    }

    public function render()
    {
        $isPlatformAdmin = $this->isPlatformAdmin();

        return view('livewire.admin.team-manager', [
            'users' => $this->getScopedUsers(),
            'roles' => $this->getAvailableRoles(),
            'roleLabels' => self::ROLE_LABELS,
            'roleBadgeLabels' => self::ROLE_BADGE_LABELS,
            'roleBadgeColors' => self::ROLE_BADGE_COLORS,
            'isPlatformAdmin' => $isPlatformAdmin,
            'platformRoles' => self::PLATFORM_ROLES,
            'tenantRoles' => self::TENANT_ROLES,
        ])->layout(
            request()->is('platform/*') ? 'layouts.platform' : 'layouts.admin',
            [
                'title' => 'Ekip Yönetimi',
                'breadcrumb' => 'Ekip Yönetimi',
            ]
        );
    }
}

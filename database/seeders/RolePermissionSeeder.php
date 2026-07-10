<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // PERMISSIONS (47 + 7 new = 54)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        $permissions = [
            // ── Resource CRUD ─────────────────────────────
            'view_clients', 'create_clients', 'edit_clients', 'delete_clients',
            'view_projects', 'create_projects', 'edit_projects', 'delete_projects',
            'view_campaigns', 'create_campaigns', 'edit_campaigns', 'delete_campaigns',
            'view_content', 'create_content', 'edit_content', 'delete_content',
            'view_tasks', 'create_tasks', 'edit_tasks', 'delete_tasks',
            'view_events', 'create_events', 'edit_events', 'delete_events',
            'view_social_posts', 'create_social_posts', 'edit_social_posts', 'delete_social_posts',
            'view_works', 'create_works', 'edit_works', 'delete_works',
            'view_proposals', 'create_proposals', 'edit_proposals', 'delete_proposals',
            'view_contracts', 'create_contracts', 'edit_contracts', 'delete_contracts',
            'view_approvals', 'manage_approvals',

            // ── Content Workflow ──────────────────────────
            'publish_content',       // Can publish drafts
            'approve_content',       // Can approve others' content

            // ── Special Access ────────────────────────────
            'view_reports', 'export_csv',
            'view_audit_log',
            'view_calendar',
            'manage_team',           // Invite/remove users, assign roles
            'manage_documents',
            'manage_email_templates',

            // ── Platform / System ─────────────────────────
            'manage_system_settings',
            'manage_tenants',
            'manage_plans',
            'manage_billing',
            'manage_announcements',
            'view_system_logs',
            'impersonate_users',
            'manage_api_keys',
            'manage_webhooks',

            // ── Tenant-Specific ───────────────────────────
            'manage_tenant_settings', // Tenant-level config
            'manage_tenant_billing',  // Tenant's own billing/subscription
            'delete_tenant',          // Can delete the tenant account itself
            'invite_guests',          // Can invite external guests
            'manage_workspaces',      // Workspace/project level management

            // ── Workspace/Project Scoped ──────────────────
            'manage_workspace_settings', // Admin a specific workspace
            'workspace_member_access',   // Work in a workspace
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $allPerms = Permission::pluck('name')->toArray();
        $viewAll = collect($allPerms)->filter(fn($p) => str_starts_with($p, 'view_'))->toArray();

        // Platform permissions (excludes tenant-specific ones)
        $platformExclude = [
            'manage_tenant_settings', 'manage_tenant_billing', 'delete_tenant',
            'invite_guests', 'manage_workspaces', 'manage_workspace_settings',
            'workspace_member_access',
        ];

        // Tenant permissions (excludes platform-level ones)
        $tenantExclude = [
            'manage_system_settings', 'manage_tenants', 'manage_plans',
            'manage_announcements', 'view_system_logs', 'impersonate_users',
            'manage_api_keys', 'manage_webhooks',
        ];

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // PLATFORM ROLES (12)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        // 1. Super Admin — ALL permissions
        $this->syncRole('super_admin', $allPerms, 'platform');

        // 2. Admin — Org-level full access
        $this->syncRole('admin', collect($allPerms)->reject(
            fn($p) => in_array($p, ['manage_system_settings', 'manage_tenants', 'manage_plans', 'impersonate_users'])
        )->toArray(), 'platform');

        // 3. Moderator — Content + user management
        $this->syncRole('moderator', array_merge($viewAll, [
            'create_content', 'edit_content', 'delete_content', 'publish_content', 'approve_content',
            'create_tasks', 'edit_tasks', 'delete_tasks',
            'create_events', 'edit_events', 'delete_events',
            'create_social_posts', 'edit_social_posts', 'delete_social_posts',
            'manage_team', 'manage_documents', 'manage_email_templates', 'export_csv',
        ]), 'platform');


        // 4. Editor — Content creation & publishing
        $this->syncRole('editor', array_merge($viewAll, [
            'create_content', 'edit_content', 'publish_content',
            'create_campaigns', 'edit_campaigns',
            'create_tasks', 'edit_tasks',
            'create_events', 'edit_events',
            'create_social_posts', 'edit_social_posts',
            'manage_documents', 'export_csv',
        ]), 'platform');

        // 5. Contributor — Creates, cannot publish
        $this->syncRole('contributor', array_merge($viewAll, [
            'create_content', 'edit_content',
            'create_tasks', 'edit_tasks',
            'create_social_posts', 'edit_social_posts',
            'manage_documents',
        ]), 'platform');

        // 6. Viewer — Read-only
        $this->syncRole('viewer', $viewAll, 'platform');

        // 7. Guest — Very limited
        $this->syncRole('guest', ['view_projects', 'view_tasks', 'view_calendar'], 'platform');

        // 8. Billing Admin
        $this->syncRole('billing_admin', ['view_clients', 'view_projects', 'view_reports', 'manage_billing', 'export_csv'], 'platform');

        // 9. Auditor — Can see everything, change nothing
        $this->syncRole('auditor', array_merge($viewAll, ['export_csv']), 'platform');

        // 10. Support Agent
        $this->syncRole('support_agent', [
            'view_clients', 'view_projects', 'view_tasks', 'view_campaigns',
            'view_content', 'view_events', 'view_reports', 'view_calendar',
            'edit_clients', 'edit_tasks',
        ], 'platform');

        // 11. Developer — Technical
        $this->syncRole('developer', array_merge($viewAll, [
            'manage_api_keys', 'manage_webhooks', 'view_system_logs', 'manage_documents', 'export_csv',
        ]), 'platform');

        // 12. API User — Machine access
        $this->syncRole('api_user', [
            'view_clients', 'view_projects', 'view_campaigns', 'view_tasks',
            'view_content', 'view_reports',
            'create_content', 'edit_content', 'create_tasks', 'edit_tasks', 'export_csv',
        ], 'platform');

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // TENANT ROLES (6) — Each tenant manages internally
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        // 1. Tenant Owner — Founded the tenant, full tenant access, CAN delete tenant
        $tenantAllPerms = collect($allPerms)->reject(fn($p) => in_array($p, $tenantExclude))->toArray();
        $this->syncRole('tenant_owner', $tenantAllPerms, 'tenant');

        // 2. Tenant Admin — Manages users & settings, CANNOT delete tenant
        $this->syncRole('tenant_admin', collect($tenantAllPerms)->reject(
            fn($p) => $p === 'delete_tenant'
        )->toArray(), 'tenant');

        // 3. Tenant Billing Admin — Only billing/subscription for tenant
        $this->syncRole('tenant_billing', [
            'view_clients', 'view_projects', 'view_reports',
            'manage_tenant_billing', 'manage_billing', 'export_csv',
        ], 'tenant');

        // 4. Tenant Member — Standard user, creates content
        $this->syncRole('tenant_member', array_merge($viewAll, [
            'create_content', 'edit_content', 'publish_content',
            'create_campaigns', 'edit_campaigns',
            'create_tasks', 'edit_tasks',
            'create_events', 'edit_events',
            'create_social_posts', 'edit_social_posts',
            'create_works', 'edit_works',
            'create_proposals', 'edit_proposals',
            'create_contracts', 'edit_contracts',
            'manage_approvals',
            'manage_documents', 'export_csv',
        ]), 'tenant');

        // 5. Tenant Viewer — Read-only
        $this->syncRole('tenant_viewer', $viewAll, 'tenant');

        // 6. Tenant Guest — Invited, limited, not a full member
        $this->syncRole('tenant_guest', [
            'view_projects', 'view_tasks', 'view_calendar',
        ], 'tenant');

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // WORKSPACE / PROJECT ROLES (3) — For future use
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        // Workspace Admin — Manages specific workspace
        $this->syncRole('workspace_admin', array_merge($viewAll, [
            'manage_workspace_settings', 'workspace_member_access',
            'create_content', 'edit_content', 'delete_content', 'publish_content',
            'create_tasks', 'edit_tasks', 'delete_tasks',
            'manage_documents', 'manage_team', 'export_csv',
        ]), 'workspace');

        // Workspace Member — Works in workspace
        $this->syncRole('workspace_member', array_merge($viewAll, [
            'workspace_member_access',
            'create_content', 'edit_content',
            'create_tasks', 'edit_tasks',
            'manage_documents',
        ]), 'workspace');

        // Workspace Viewer — Views workspace only
        $this->syncRole('workspace_viewer', array_merge(
            ['view_projects', 'view_tasks', 'view_calendar', 'view_content'],
            ['workspace_member_access']
        ), 'workspace');

        // ── Assign super_admin to user #2 ────────────
        $user = \App\Models\User::find(2);
        if ($user) {
            $user->syncRoles(['super_admin']);
        }
    }

    private function syncRole(string $name, array $permissions, string $scope = 'platform'): Role
    {
        $role = Role::firstOrCreate(['name' => $name]);
        $role->update(['scope' => $scope]);
        $role->syncPermissions($permissions);
        return $role;
    }
}

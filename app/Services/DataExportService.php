<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * KVKK-compliant data export and account deletion service.
 *
 * - exportUserData(): JSON export of all user's tenant-scoped data
 * - anonymizeUser(): Soft-anonymize user data (preserves audit integrity)
 */
class DataExportService
{
    /**
     * Export all data belonging to the user's tenant.
     * Returns array ready for JSON encoding.
     */
    public function exportUserData(User $user): array
    {
        $tenantId = $user->tenant_id;

        if (!$tenantId) {
            return ['error' => 'User is not associated with a tenant.'];
        }

        $data = [
            'export_date' => now()->toIso8601String(),
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ];

        // Projects
        $data['projects'] = DB::table('projects')
            ->where('tenant_id', $tenantId)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();

        // Tasks
        $data['tasks'] = DB::table('tasks')
            ->where('tenant_id', $tenantId)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();

        // Clients
        $data['clients'] = DB::table('clients')
            ->where('tenant_id', $tenantId)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();

        // Campaigns (via department — tenant-scoped)
        if (\Schema::hasColumn('campaigns', 'tenant_id')) {
            $data['campaigns'] = DB::table('campaigns')
                ->where('tenant_id', $tenantId)
                ->get()
                ->map(fn($row) => (array) $row)
                ->toArray();
        }

        // Time entries
        if (\Schema::hasTable('time_entries')) {
            $data['time_entries'] = DB::table('time_entries')
                ->where('tenant_id', $tenantId)
                ->get()
                ->map(fn($row) => (array) $row)
                ->toArray();
        }

        // Audit log (user's own actions only)
        $data['audit_log'] = DB::table('audits')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5000)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();

        return $data;
    }

    /**
     * Anonymize user data for KVKK "right to be forgotten".
     *
     * Instead of hard-deleting (which breaks audit trail integrity),
     * we anonymize PII fields and deactivate the account.
     */
    public function anonymizeUser(User $user): void
    {
        $hash = hash('sha256', $user->email . $user->id);

        // Store deletion record
        DB::table('deleted_users')->insert([
            'original_user_id' => $user->id,
            'email_hash'       => $hash,
            'tenant_id'        => $user->tenant_id,
            'deleted_at'       => now(),
            'reason'           => 'KVKK — kullanıcı talebi',
        ]);

        // Anonymize user
        $user->update([
            'name'              => 'Silinmiş Kullanıcı',
            'email'             => "silinen-{$user->id}@anonymized.local",
            'password'          => bcrypt(\Str::random(64)),
            'remember_token'    => null,
            'two_factor_enabled' => false,
            'two_factor_secret'  => null,
            'two_factor_recovery_codes' => null,
        ]);

        // Remove from all roles
        if (method_exists($user, 'roles')) {
            $user->roles()->detach();
        }

        // Log the anonymization
        \Log::info("KVKK: User #{$user->id} anonymized. Hash: {$hash}");
    }
}

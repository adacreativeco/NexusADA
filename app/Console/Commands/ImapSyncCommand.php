<?php

namespace App\Console\Commands;

use App\Models\EmailAccount;
use App\Services\ImapSyncService;
use Illuminate\Console\Command;

class ImapSyncCommand extends Command
{
    protected $signature = 'imap:sync
                            {--account= : Specific email account ID to sync}
                            {--force : Ignore sync interval, sync immediately}';

    protected $description = 'Synchronize emails from IMAP accounts';

    public function handle(ImapSyncService $service): int
    {
        $accountId = $this->option('account');
        $force = $this->option('force');

        if ($accountId) {
            $account = EmailAccount::find($accountId);

            if (!$account) {
                $this->error("Email account #{$accountId} not found.");
                return self::FAILURE;
            }

            if (!$account->is_active) {
                $this->warn("Account #{$accountId} is inactive.");
                return self::SUCCESS;
            }

            $count = $service->syncAccount($account);
            $this->info("Synced {$account->email}: {$count} new emails.");
        } else {
            // Sync all due accounts
            if ($force) {
                $accounts = EmailAccount::active()->get();
            } else {
                $accounts = EmailAccount::dueForSync()->get();
            }

            if ($accounts->isEmpty()) {
                $this->info('No accounts due for sync.');
                return self::SUCCESS;
            }

            $totalImported = 0;

            foreach ($accounts as $account) {
                $count = $service->syncAccount($account);
                $this->info("  {$account->email}: {$count} new emails" . ($account->last_error ? " ⚠ {$account->last_error}" : ''));
                $totalImported += $count;
            }

            $this->newLine();
            $this->info("Total: {$accounts->count()} accounts synced, {$totalImported} new emails.");
        }

        return self::SUCCESS;
    }
}

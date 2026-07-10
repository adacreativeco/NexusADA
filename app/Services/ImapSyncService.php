<?php

namespace App\Services;

use App\Models\EmailAccount;
use App\Models\IncomingEmail;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\ClientManager;

class ImapSyncService
{
    /**
     * Sync a single email account.
     * Returns the number of new emails imported.
     */
    public function syncAccount(EmailAccount $account): int
    {
        $imported = 0;

        try {
            $cm = new ClientManager();
            $client = $cm->make($account->toImapConfig());

            $client->connect();

            $folder = $client->getFolder($account->folder);

            if (!$folder) {
                $account->markError("Folder '{$account->folder}' not found.");
                return 0;
            }

            // Build query: fetch messages since last sync, or last 7 days for first sync
            $since = $account->last_sync_at
                ? $account->last_sync_at->subMinutes(5) // overlap to catch edge cases
                : now()->subDays(7);

            $messages = $folder->query()
                ->since($since->toDateString())
                ->setFetchBody(true)
                ->setFetchFlags(true)
                ->get();

            foreach ($messages as $message) {
                $messageId = $message->getMessageId()?->toString();
                $uid = $message->getUid();

                // Skip if no message ID (malformed)
                if (!$messageId) {
                    $messageId = 'uid-' . $uid . '-' . $account->id;
                }

                // Duplicate check
                $exists = IncomingEmail::where('email_account_id', $account->id)
                    ->where('message_id', $messageId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Extract fields
                $from = $message->getFrom();
                $fromAddress = $from[0]?->mail ?? 'unknown';
                $fromName = $from[0]?->personal ?? null;

                $to = collect($message->getTo() ?? [])
                    ->map(fn($addr) => $addr->mail)
                    ->implode(', ');

                $cc = collect($message->getCc() ?? [])
                    ->map(fn($addr) => $addr->mail)
                    ->implode(', ');

                $subject = $message->getSubject()?->toString() ?? '(Konusuz)';
                $bodyText = $message->getTextBody() ?? '';
                $bodyHtml = $message->getHTMLBody() ?? '';
                $hasAttachments = $message->hasAttachments();
                $date = $message->getDate()?->toDate() ?? now();

                // Sanitize HTML body (strip dangerous tags, keep structure)
                $bodyHtml = $this->sanitizeHtml($bodyHtml);

                IncomingEmail::create([
                    'tenant_id'        => $account->tenant_id,
                    'email_account_id' => $account->id,
                    'message_id'       => $messageId,
                    'uid'              => $uid,
                    'from_address'     => $fromAddress,
                    'from_name'        => $fromName,
                    'to'               => $to ?: null,
                    'cc'               => $cc ?: null,
                    'subject'          => mb_substr($subject, 0, 500),
                    'body'             => mb_substr($bodyText, 0, 65000),
                    'body_html'        => $bodyHtml ? mb_substr($bodyHtml, 0, 65000) : null,
                    'status'           => 'unread',
                    'folder'           => $account->folder,
                    'has_attachments'  => $hasAttachments,
                    'received_at'      => $date,
                ]);

                $imported++;
            }

            $client->disconnect();
            $account->markSynced();

        } catch (\Throwable $e) {
            $errorMsg = mb_substr($e->getMessage(), 0, 500);
            $account->markError($errorMsg);
            Log::error("IMAP sync failed for account #{$account->id}: {$errorMsg}");
        }

        return $imported;
    }

    /**
     * Sync all accounts that are due for synchronization.
     */
    public function syncAllDue(): array
    {
        $accounts = EmailAccount::dueForSync()->get();
        $results = [];

        foreach ($accounts as $account) {
            $count = $this->syncAccount($account);
            $results[$account->email] = $count;
        }

        return $results;
    }

    /**
     * Test connection to an IMAP account without importing emails.
     */
    public function testConnection(EmailAccount $account): array
    {
        try {
            $cm = new ClientManager();
            $client = $cm->make($account->toImapConfig());
            $client->connect();

            $folders = $client->getFolders();
            $folderNames = collect($folders)->map(fn($f) => $f->name)->toArray();

            $client->disconnect();

            return [
                'success' => true,
                'message' => 'Bağlantı başarılı.',
                'folders' => $folderNames,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
                'folders' => [],
            ];
        }
    }

    /**
     * Strip dangerous HTML tags while preserving structure for display.
     */
    private function sanitizeHtml(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Remove script, style, iframe, object, embed tags entirely
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|button)[^>]*>.*?<\/\1>/si', '', $html);
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|button)[^>]*\/?>/si', '', $html);

        // Remove on* event handlers
        $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*\S+/i', '', $html);

        // Remove javascript: protocol
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', 'href="#"', $html);

        return $html;
    }
}

<?php

namespace App\Services\Email;

use App\Models\IncomingEmail;
use Illuminate\Support\Str;

class EmailThreadingService
{
    /**
     * Determine or generate conversation thread ID for an incoming email
     */
    public static function resolveThreadId(array $headers, ?int $tenantId = null): string
    {
        $inReplyTo = $headers['in_reply_to'] ?? null;
        $references = $headers['references'] ?? null;
        $subject = self::cleanSubject($headers['subject'] ?? '');

        // 1. Check if In-Reply-To matches existing email
        if ($inReplyTo) {
            $parent = IncomingEmail::where('message_id', $inReplyTo)->first();
            if ($parent && !empty($parent->thread_id)) {
                return $parent->thread_id;
            }
        }

        // 2. Check if Subject matches active thread within same tenant
        if (!empty($subject) && $tenantId) {
            $existing = IncomingEmail::where('tenant_id', $tenantId)
                ->where('subject', 'LIKE', "%{$subject}%")
                ->whereNotNull('thread_id')
                ->latest()
                ->first();

            if ($existing) {
                return $existing->thread_id;
            }
        }

        return 'thread_' . Str::random(24);
    }

    public static function cleanSubject(string $subject): string
    {
        return trim(preg_replace('/^(Re|Fwd|Ynt|İlt):\s*/i', '', $subject));
    }
}

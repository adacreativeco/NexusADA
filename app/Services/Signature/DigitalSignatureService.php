<?php

namespace App\Services\Signature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalSignatureService
{
    /**
     * Generate a cryptographic digital sign certificate for a proposal or contract
     */
    public static function signDocument(Model $document, array $signatoryData): array
    {
        $signerName = $signatoryData['name'] ?? 'Yetkili';
        $signerEmail = $signatoryData['email'] ?? 'client@example.com';
        $ip = $signatoryData['ip'] ?? request()->ip() ?? '127.0.0.1';
        $userAgent = $signatoryData['user_agent'] ?? request()->userAgent() ?? 'NexusADA-Browser';
        $timestamp = now()->toIso8601String();

        $rawPayload = json_encode([
            'doc_type' => get_class($document),
            'doc_id' => $document->id,
            'title' => $document->title ?? $document->name ?? '',
            'amount' => (float)($document->grand_total ?? $document->total_amount ?? $document->amount ?? 0),
            'signer_name' => $signerName,
            'signer_email' => $signerEmail,
            'ip' => $ip,
            'timestamp' => $timestamp,
        ], JSON_UNESCAPED_UNICODE);

        $signatureHash = hash_hmac('sha256', $rawPayload, config('app.key'));
        $certificateId = 'CERT-' . strtoupper(Str::random(12));

        $certificate = [
            'certificate_id' => $certificateId,
            'signature_hash' => $signatureHash,
            'signer_name' => $signerName,
            'signer_email' => $signerEmail,
            'ip_address' => $ip,
            'signed_at' => $timestamp,
            'user_agent' => $userAgent,
            'status' => 'verified_valid',
        ];

        // Update document status
        if (method_exists($document, 'update')) {
            $document->update([
                'status' => 'accepted',
                'notes' => json_encode($certificate, JSON_UNESCAPED_UNICODE),
            ]);
        }

        return $certificate;
    }

    /**
     * Verify the integrity of a signed document certificate
     */
    public static function verifyCertificate(Model $document, array $certificate): bool
    {
        if (empty($certificate['signature_hash']) || empty($certificate['signed_at'])) {
            return false;
        }

        $rawPayload = json_encode([
            'doc_type' => get_class($document),
            'doc_id' => $document->id,
            'title' => $document->title ?? $document->name ?? '',
            'amount' => (float)($document->grand_total ?? $document->total_amount ?? $document->amount ?? 0),
            'signer_name' => $certificate['signer_name'] ?? '',
            'signer_email' => $certificate['signer_email'] ?? '',
            'ip' => $certificate['ip_address'] ?? '',
            'timestamp' => $certificate['signed_at'],
        ], JSON_UNESCAPED_UNICODE);

        $expectedHash = hash_hmac('sha256', $rawPayload, config('app.key'));
        return hash_equals($expectedHash, $certificate['signature_hash']);
    }
}

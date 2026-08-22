<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\Task;
use App\Notifications\InvoicePaidNotification;
use App\Notifications\ProposalSentNotification;
use App\Events\TaskUpdatedEvent;
use App\Events\NotificationCreatedEvent;
use App\Services\Storage\CloudStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Nexus Hardening Corp',
            'slug' => 'nexus-hardening',
            'email' => 'hardening@nexus.com',
        ]);

        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'email' => 'admin@hardening.com',
        ]);

        $this->client = Client::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Acme Client',
            'email' => 'client@acme.com',
        ]);
    }

    public function test_stripe_and_iyzico_webhooks_mark_invoice_paid(): void
    {
        $invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'invoice_number' => 'INV-WEBHOOK-001',
            'amount' => 25000,
            'currency' => 'TRY',
            'status' => 'unpaid',
            'billing_period_start' => now(),
            'billing_period_end' => now()->addMonth(),
        ]);

        // Stripe Webhook
        $stripePayload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_stripe_999',
                    'metadata' => [
                        'invoice_id' => $invoice->id,
                    ],
                ],
            ],
        ];

        $resStripe = $this->postJson('/api/webhooks/stripe', $stripePayload);
        $resStripe->assertStatus(200);
        $resStripe->assertJson(['status' => 'handled']);
        $this->assertEquals('paid', $invoice->fresh()->status);

        // Iyzico Webhook
        $invoice2 = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'invoice_number' => 'INV-WEBHOOK-002',
            'amount' => 12000,
            'currency' => 'TRY',
            'status' => 'unpaid',
            'billing_period_start' => now(),
            'billing_period_end' => now()->addMonth(),
        ]);

        $iyzicoPayload = [
            'status' => 'SUCCESS',
            'paymentId' => 'iyzi_pay_888',
            'invoiceId' => $invoice2->id,
        ];

        $resIyzi = $this->postJson('/api/webhooks/iyzico', $iyzicoPayload);
        $resIyzi->assertStatus(200);
        $this->assertEquals('paid', $invoice2->fresh()->status);
    }

    public function test_transactional_email_notifications(): void
    {
        Notification::fake();

        $invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'invoice_number' => 'INV-MAIL-001',
            'amount' => 5000,
            'currency' => 'TRY',
            'status' => 'paid',
            'billing_period_start' => now(),
            'billing_period_end' => now()->addMonth(),
        ]);

        $this->user->notify(new InvoicePaidNotification($invoice));
        Notification::assertSentTo($this->user, InvoicePaidNotification::class);

        $proposal = Proposal::create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'proposal_number' => 'PROP-MAIL-001',
            'title' => 'Kurumsal Dijital Pazarlama Teklifi',
            'grand_total' => 30000,
            'status' => 'sent',
        ]);

        $this->user->notify(new ProposalSentNotification($proposal));
        Notification::assertSentTo($this->user, ProposalSentNotification::class);
    }

    public function test_broadcast_events_payload(): void
    {
        $task = Task::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'WebSocket Live Broadcast Task',
            'status' => 'in_progress',
        ]);

        $event = new TaskUpdatedEvent($task, 'status_changed');
        $channels = $event->broadcastOn();

        $this->assertNotEmpty($channels);
        $this->assertEquals('task.updated', $event->broadcastAs());
        $this->assertEquals('private-tenant.' . $this->tenant->id, $channels[0]->name);
    }

    public function test_cloud_storage_tenant_isolated_upload(): void
    {
        $fileContent = "Sample Brand Asset Content";
        $upload = CloudStorageService::uploadTenantAsset($this->tenant->id, 'logo.png', $fileContent, 'brand');

        $this->assertNotEmpty($upload['path']);
        $this->assertStringContainsString('tenants/' . $this->tenant->id . '/brand', $upload['path']);
        $this->assertEquals(strlen($fileContent), $upload['size']);
    }
}

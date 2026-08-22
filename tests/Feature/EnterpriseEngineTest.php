<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Client;
use App\Models\Task;
use App\Models\Work;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\IncomingEmail;
use App\Services\AI\AIGateway;
use App\Services\AI\VectorStore;
use App\Services\AI\AIToolRegistry;
use App\Services\Workflow\ConditionEvaluator;
use App\Services\Workflow\ApprovalGateService;
use App\Services\Payment\PaymentService;
use App\Services\Signature\DigitalSignatureService;
use App\Services\Email\EmailThreadingService;
use App\Services\Email\EmailIntelligenceService;
use App\Services\Planning\CriticalPathEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnterpriseEngineTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Acme Enterprise',
            'slug' => 'acme-corp',
            'email' => 'acme@example.com',
        ]);

        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'email' => 'admin@acme.com',
        ]);

        $this->client = Client::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Global Logistics Inc.',
            'email' => 'john@global.com',
        ]);
    }

    public function test_ai_gateway_and_vector_store_engine(): void
    {
        $gateway = new AIGateway();
        $response = $gateway->ask('Lütfen günlük brifing hazırla', '', ['context_type' => 'daily_briefing']);
        
        $this->assertNotEmpty($response);
        $this->assertStringContainsString('Brifingi', $response);

        // Vector Cosine Similarity
        $vecA = [1.0, 0.0, 1.0];
        $vecB = [1.0, 0.0, 1.0];
        $similarity = VectorStore::cosineSimilarity($vecA, $vecB);
        $this->assertEqualsWithDelta(1.0, $similarity, 0.001);

        // AI Tool Registry
        $toolResult = AIToolRegistry::executeTool('create_task', [
            'tenant_id' => $this->tenant->id,
            'title' => 'AI Generated Audit Task',
        ]);
        $this->assertEquals('success', $toolResult['status']);
        $this->assertDatabaseHas('tasks', ['title' => 'AI Generated Audit Task']);
    }

    public function test_conditional_workflow_and_approval_gates(): void
    {
        // 1. Condition Evaluator
        $evalTrue = ConditionEvaluator::evaluate(['field' => 'budget', 'operator' => '>', 'value' => 5000], ['budget' => 12000]);
        $evalFalse = ConditionEvaluator::evaluate(['field' => 'budget', 'operator' => '>', 'value' => 20000], ['budget' => 12000]);
        $this->assertTrue($evalTrue);
        $this->assertFalse($evalFalse);

        // 2. Approval Gates
        $work = Work::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Kritik Web Tasarım İşi',
            'status' => 'active',
            'budget' => 50000,
        ]);

        $gate = ApprovalGateService::createGate($work, 'Müdür Bütçe Onayı', 'manager');
        $this->assertEquals('submitted', $gate->action);

        ApprovalGateService::resolveGate($gate, true, 'Bütçe onaylandı.');
        $this->assertEquals('approved', $gate->fresh()->action);
    }

    public function test_payment_gateways_and_digital_signature_integrity(): void
    {
        // 1. Payment Service
        $invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'invoice_number' => 'INV-2026-001',
            'amount' => 15000,
            'currency' => 'TRY',
            'status' => 'unpaid',
            'billing_period_start' => now(),
            'billing_period_end' => now()->addMonth(),
        ]);

        $session = PaymentService::initiateInvoicePayment($invoice, 'mock');
        $this->assertEquals('success', $session['status']);
        $this->assertNotEmpty($session['session_id']);

        PaymentService::markInvoicePaid($invoice, 'mock_txn_999', 'mock_sandbox');
        $this->assertEquals('paid', $invoice->fresh()->status);

        // 2. Digital Signatures
        $proposal = Proposal::create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'proposal_number' => 'PROP-2026-001',
            'title' => 'Yıllık Bakım Teklifi',
            'grand_total' => 45000,
            'status' => 'draft',
        ]);

        $cert = DigitalSignatureService::signDocument($proposal, [
            'name' => 'Ahmet Yılmaz',
            'email' => 'ahmet@client.com',
            'ip' => '192.168.1.1',
        ]);

        $this->assertNotEmpty($cert['signature_hash']);
        $this->assertEquals('accepted', $proposal->fresh()->status);

        $isValid = DigitalSignatureService::verifyCertificate($proposal, $cert);
        $this->assertTrue($isValid);
    }

    public function test_email_intelligence_and_threading(): void
    {
        $threadId1 = EmailThreadingService::resolveThreadId(['subject' => 'Teklif İnceleme Talebi'], $this->tenant->id);
        $this->assertNotEmpty($threadId1);

        $email = IncomingEmail::create([
            'tenant_id' => $this->tenant->id,
            'from' => 'client@acme.com',
            'from_name' => 'Client User',
            'subject' => 'ACİL: Sunucu Hatası Hakkında',
            'body' => 'Sistemde kesinti yaşanıyor, lütfen acil destek verin.',
            'status' => 'unread',
        ]);

        $result = EmailIntelligenceService::processInboundEmail($email);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result['analysis']);
        $this->assertNotNull($result['task_id']);
    }

    public function test_critical_path_method_planning(): void
    {
        $tasks = [
            ['id' => 1, 'name' => 'Analiz', 'duration' => 2, 'dependencies' => []],
            ['id' => 2, 'name' => 'Tasarım', 'duration' => 4, 'dependencies' => [1]],
            ['id' => 3, 'name' => 'Geliştirme', 'duration' => 6, 'dependencies' => [2]],
            ['id' => 4, 'name' => 'Dokümantasyon', 'duration' => 1, 'dependencies' => [1]],
        ];

        $cpm = CriticalPathEngine::calculate($tasks);

        $this->assertEquals(12, $cpm['project_duration_days']); // 2 + 4 + 6 = 12
        $this->assertContains(1, $cpm['critical_path_task_ids']);
        $this->assertContains(2, $cpm['critical_path_task_ids']);
        $this->assertContains(3, $cpm['critical_path_task_ids']);
        $this->assertNotContains(4, $cpm['critical_path_task_ids']); // Task 4 has float
    }
}

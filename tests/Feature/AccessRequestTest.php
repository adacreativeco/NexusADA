<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\AccessRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_request_can_be_created(): void
    {
        $ar = AccessRequest::create([
            'name' => 'Test Firma',
            'company_name' => 'Test Corp',
            'email' => 'test@corp.com',
            'plan_interest' => 'pro',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('access_requests', [
            'email' => 'test@corp.com',
            'plan_interest' => 'pro',
            'status' => 'pending',
        ]);
    }

    public function test_access_request_status_can_be_updated(): void
    {
        $ar = AccessRequest::create([
            'name' => 'Test', 'company_name' => 'Corp',
            'email' => 'test@test.com', 'plan_interest' => 'enterprise',
            'status' => 'pending',
        ]);

        $ar->update(['status' => 'approved']);
        $this->assertEquals('approved', $ar->fresh()->status);
    }

    public function test_access_request_scopes(): void
    {
        AccessRequest::create([
            'name' => 'Pending', 'company_name' => 'A',
            'email' => 'a@a.com', 'plan_interest' => 'pro', 'status' => 'pending',
        ]);
        AccessRequest::create([
            'name' => 'Approved', 'company_name' => 'B',
            'email' => 'b@b.com', 'plan_interest' => 'pro', 'status' => 'approved',
        ]);

        $this->assertEquals(1, AccessRequest::where('status', 'pending')->count());
        $this->assertEquals(1, AccessRequest::where('status', 'approved')->count());
    }
}

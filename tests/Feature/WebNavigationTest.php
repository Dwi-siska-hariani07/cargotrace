<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WebNavigationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_pages_load_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $trackResponse = $this->get('/track?number=CRG-2026-8801');
        $trackResponse->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_operator_can_access_dashboard_and_shipments(): void
    {
        $operator = User::where('role', 'operator')->first();

        $response = $this->actingAs($operator)->get('/dashboard');
        $response->assertStatus(200);

        $shipmentsResponse = $this->actingAs($operator)->get('/shipments');
        $shipmentsResponse->assertStatus(200);
    }

    public function test_customer_cannot_access_audit_logs(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get('/audit-logs');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_audit_logs(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/audit-logs');
        $response->assertStatus(200);
    }
}

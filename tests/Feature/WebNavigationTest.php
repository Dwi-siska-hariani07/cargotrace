<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shipment;
use App\Models\Notification;
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

    public function test_customer_can_access_notifications_inbox(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get('/notifications');
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_users_management(): void
    {
        $customer = User::where('role', 'customer')->first();
        $operator = User::where('role', 'operator')->first();

        $this->actingAs($customer)->get('/users')->assertStatus(403);
        $this->actingAs($operator)->get('/users')->assertStatus(403);
    }

    public function test_admin_can_access_and_create_users(): void
    {
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->get('/users')->assertStatus(200);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Test Operator Baru',
            'email' => 'testoperator_new@cargotrace.com',
            'password' => 'password123',
            'role' => 'operator',
            'company' => 'CargoTrace Test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'testoperator_new@cargotrace.com',
            'role' => 'operator',
        ]);
    }

    public function test_notification_ownership_is_enforced(): void
    {
        $customer = User::where('role', 'customer')->first();
        $operator = User::where('role', 'operator')->first();

        $notif = Notification::where('user_id', $customer->id)->first();
        $this->assertNotNull($notif);

        // Another user cannot mark customer's notification read
        $this->actingAs($operator)->post("/notifications/{$notif->id}/read")->assertStatus(403);

        // Customer can mark their own notification read
        $this->actingAs($customer)->post("/notifications/{$notif->id}/read")->assertRedirect();
        $this->assertTrue($notif->fresh()->is_read);
    }
}


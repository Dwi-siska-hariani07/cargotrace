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

    public function test_language_dictionaries_have_matching_keys(): void
    {
        $id = require base_path('lang/id/ui.php');
        $en = require base_path('lang/en/ui.php');

        $flatten = function (array $items, string $prefix = '') use (&$flatten): array {
            $keys = [];
            foreach ($items as $k => $v) {
                $full = $prefix !== '' ? "{$prefix}.{$k}" : $k;
                if (is_array($v)) {
                    $keys = array_merge($keys, $flatten($v, $full));
                } else {
                    $keys[] = $full;
                }
            }
            return $keys;
        };

        $idKeys = $flatten($id);
        $enKeys = $flatten($en);

        $this->assertEmpty(array_diff($idKeys, $enKeys), 'Missing EN keys for ID keys');
        $this->assertEmpty(array_diff($enKeys, $idKeys), 'Missing ID keys for EN keys');
    }

    public function test_locale_switching_changes_rendered_language(): void
    {
        $this->get('/locale/en')->assertRedirect();
        $this->get('/')->assertStatus(200)->assertSee('Track Global Vessels');

        $this->get('/locale/id')->assertRedirect();
        $this->get('/')->assertStatus(200)->assertSee('Pantau Pergerakan Kapal');
    }

    public function test_public_waybill_and_realistic_tracking_features_render(): void
    {
        $this->get('/locale/id')->assertRedirect();

        $home = $this->get('/');
        $home->assertStatus(200)
            ->assertSee('quote-calculator')
            ->assertSee('CRG-2026-8809')
            ->assertSee('PT CargoTrace Global Logistik Tbk');

        // 1. Guest can track vessel & telemetry, but waybill print is protected
        $track = $this->get('/track?number=CRG-2026-8801');
        $track->assertStatus(200)
            ->assertSee('MV Maersk Mc-Kinney Moller V.2608')
            ->assertSee('MSKU 904281-4')
            ->assertSee('Progres Tahapan Pengiriman')
            ->assertSee('Dalam Perjalanan')
            ->assertSee('Dokumen Resmi (Khusus Pemilik)')
            ->assertSee('Akses Pelacakan Terbuka vs Perlindungan Dokumen Hukum');

        // 2. Unauthenticated guest attempting to print waybill is redirected to login
        $guestWaybill = $this->get('/track/waybill?number=CRG-2026-8803');
        $guestWaybill->assertRedirect('/login');

        // 3. Customer owning the shipment can view and print the waybill
        $customer2 = User::where('email', 'acme@corp.com')->first();
        $this->assertNotNull($customer2);

        $authWaybill = $this->actingAs($customer2)->get('/track/waybill?number=CRG-2026-8803');
        $authWaybill->assertStatus(200)
            ->assertSee('CRG-2026-8803')
            ->assertSee('e-POD SIGNED')
            ->assertSee('AWB 618-44901284');

        // 4. Customer attempting to access another customer's waybill is rejected (403)
        $customer1 = User::where('email', 'customer@cargotrace.com')->first();
        $this->assertNotNull($customer1);

        $forbiddenWaybill = $this->actingAs($customer1)->get('/track/waybill?number=CRG-2026-8803');
        $forbiddenWaybill->assertStatus(403);

        // 5. Operator or Admin can access waybill for any shipment
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $adminWaybill = $this->actingAs($admin)->get('/track/waybill?number=CRG-2026-8803');
        $adminWaybill->assertStatus(200)
            ->assertSee('CRG-2026-8803');
    }
}


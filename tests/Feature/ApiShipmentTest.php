<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiShipmentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unauthenticated_user_cannot_access_shipments(): void
    {
        $response = $this->getJson('/api/shipments');
        $response->assertStatus(401);
    }

    public function test_login_returns_token_and_user_profile(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'operator@cargotrace.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
            ]);
    }

    public function test_invalid_login_returns_401(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'operator@cargotrace.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid login credentials.',
            ]);
    }

    public function test_customer_can_only_see_their_own_shipments(): void
    {
        $customer1 = User::where('email', 'customer@cargotrace.com')->first();
        $otherCustomerShipment = Shipment::where('customer_id', '!=', $customer1->id)->first();

        // Customer cannot access other customer shipment
        $response = $this->actingAs($customer1, 'sanctum')->getJson("/api/shipments/{$otherCustomerShipment->id}");
        $response->assertStatus(403);

        // Customer can access own shipment
        $ownShipment = Shipment::where('customer_id', $customer1->id)->first();
        $responseOwn = $this->actingAs($customer1, 'sanctum')->getJson("/api/shipments/{$ownShipment->id}");
        $responseOwn->assertStatus(200)
            ->assertJsonPath('data.shipment_number', $ownShipment->shipment_number);
    }
}


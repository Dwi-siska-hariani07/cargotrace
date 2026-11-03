<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shipment;
use App\Services\ShipmentService;
use App\Services\EtaService;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShipmentLifecycleTest extends TestCase
{
    use DatabaseTransactions;

    protected ShipmentService $shipmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shipmentService = app(ShipmentService::class);
    }

    public function test_valid_status_transitions(): void
    {
        $operator = User::where('role', 'operator')->first();
        $shipment = Shipment::where('current_status', 'IN_TRANSIT')->first();

        // IN_TRANSIT -> DELIVERED is valid
        $this->assertTrue($this->shipmentService->canTransition('IN_TRANSIT', 'DELIVERED'));

        $updated = $this->shipmentService->transitionStatus($shipment, 'DELIVERED', $operator, 'Arrived at destination.');
        $this->assertEquals('DELIVERED', $updated->current_status);
        $this->assertNotNull($updated->actual_arrival);
    }

    public function test_invalid_status_transition_from_terminal_delivered(): void
    {
        $operator = User::where('role', 'operator')->first();
        $shipment = Shipment::where('current_status', 'DELIVERED')->first();

        // DELIVERED cannot transition back to CREATED or BOOKED
        $this->assertFalse($this->shipmentService->canTransition('DELIVERED', 'CREATED'));

        $this->expectException(ValidationException::class);
        $this->shipmentService->transitionStatus($shipment, 'CREATED', $operator);
    }
}

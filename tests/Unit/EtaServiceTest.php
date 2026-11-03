<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EtaService;
use App\Models\Shipment;
use Carbon\Carbon;

class EtaServiceTest extends TestCase
{
    protected EtaService $etaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->etaService = new EtaService();
    }

    public function test_it_correctly_identifies_on_time_shipment(): void
    {
        $now = Carbon::now();
        $shipment = new Shipment([
            'expected_arrival' => $now->copy()->addDays(5),
            'estimated_arrival' => $now->copy()->addDays(5),
        ]);

        $this->etaService->evaluateDelay($shipment);

        $this->assertFalse($shipment->is_delayed);
        $this->assertEquals(0, $shipment->delay_hours);
        $this->assertEquals('NONE', $shipment->delay_severity);
    }

    public function test_it_correctly_calculates_low_severity_delay(): void
    {
        $now = Carbon::now();
        $shipment = new Shipment([
            'expected_arrival' => $now->copy()->addDays(2),
            'estimated_arrival' => $now->copy()->addDays(2)->addHours(4),
        ]);

        $this->etaService->evaluateDelay($shipment);

        $this->assertTrue($shipment->is_delayed);
        $this->assertEquals(4, $shipment->delay_hours);
        $this->assertEquals('LOW', $shipment->delay_severity);
    }

    public function test_it_correctly_calculates_medium_severity_delay(): void
    {
        $now = Carbon::now();
        $shipment = new Shipment([
            'expected_arrival' => $now->copy()->addDays(2),
            'estimated_arrival' => $now->copy()->addDays(2)->addHours(16),
        ]);

        $this->etaService->evaluateDelay($shipment);

        $this->assertTrue($shipment->is_delayed);
        $this->assertEquals(16, $shipment->delay_hours);
        $this->assertEquals('MEDIUM', $shipment->delay_severity);
    }

    public function test_it_correctly_calculates_high_severity_delay(): void
    {
        $now = Carbon::now();
        $shipment = new Shipment([
            'expected_arrival' => $now->copy()->addDays(2),
            'estimated_arrival' => $now->copy()->addDays(2)->addHours(36),
        ]);

        $this->etaService->evaluateDelay($shipment);

        $this->assertTrue($shipment->is_delayed);
        $this->assertEquals(36, $shipment->delay_hours);
        $this->assertEquals('HIGH', $shipment->delay_severity);
    }
}

<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Thresholds in hours for delay severity:
     * 0 to 6 hours: LOW
     * > 6 to 24 hours: MEDIUM
     * > 24 hours: HIGH
     */
    public const THRESHOLD_LOW_MAX = 6;
    public const THRESHOLD_MEDIUM_MAX = 24;

    /**
     * Evaluate and update delay status for a shipment based on expected vs estimated arrival.
     */
    public function evaluateDelay(Shipment $shipment, ?string $reason = null): Shipment
    {
        $expected = Carbon::parse($shipment->expected_arrival);
        $estimated = Carbon::parse($shipment->estimated_arrival);

        if ($estimated->gt($expected)) {
            // Difference in hours
            $delayHours = (int) ceil($expected->diffInMinutes($estimated) / 60);
            $severity = $this->calculateSeverity($delayHours);

            $shipment->is_delayed = true;
            $shipment->delay_hours = $delayHours;
            $shipment->delay_severity = $severity;
            if ($reason) {
                $shipment->delay_reason = $reason;
            } elseif (empty($shipment->delay_reason)) {
                $shipment->delay_reason = "Estimated arrival exceeds scheduled arrival by {$delayHours} hours.";
            }
        } else {
            $shipment->is_delayed = false;
            $shipment->delay_hours = 0;
            $shipment->delay_severity = 'NONE';
        }

        if ($shipment->exists) {
            $shipment->save();
        }
        return $shipment;
    }

    /**
     * Calculate delay severity.
     */
    public function calculateSeverity(int $delayHours): string
    {
        if ($delayHours <= 0) {
            return 'NONE';
        }

        if ($delayHours <= self::THRESHOLD_LOW_MAX) {
            return 'LOW';
        }

        if ($delayHours <= self::THRESHOLD_MEDIUM_MAX) {
            return 'MEDIUM';
        }

        return 'HIGH';
    }
}

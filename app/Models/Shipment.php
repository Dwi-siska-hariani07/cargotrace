<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'transport_type',
        'cargo_type',
        'vessel_or_flight',
        'container_or_awb',
        'receiver_name',
        'total_weight_kg',
        'total_volume_cbm',
        'expected_departure',
        'expected_arrival',
        'estimated_arrival',
        'actual_departure',
        'actual_arrival',
        'current_status',
        'is_delayed',
        'delay_hours',
        'delay_severity',
        'delay_reason',
        'current_latitude',
        'current_longitude',
        'current_location_name',
        'notes',
    ];

    protected $casts = [
        'expected_departure' => 'datetime',
        'expected_arrival' => 'datetime',
        'estimated_arrival' => 'datetime',
        'actual_departure' => 'datetime',
        'actual_arrival' => 'datetime',
        'is_delayed' => 'boolean',
        'delay_hours' => 'integer',
        'total_weight_kg' => 'float',
        'total_volume_cbm' => 'float',
        'current_latitude' => 'float',
        'current_longitude' => 'float',
    ];

    /**
     * Return localized human-readable label for the current shipment status.
     */
    public function statusLabel(): string
    {
        $key = 'ui.status.' . $this->current_status;
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return str_replace('_', ' ', (string) $this->current_status);
    }

    /**
     * Map current_status to 1..5 visual progress step (0 when cancelled).
     */
    public function progressStep(): int
    {
        return match ($this->current_status) {
            'CREATED', 'BOOKED' => 1,
            'PICKED_UP' => 2,
            'IN_TRANSIT' => 3,
            'OUT_FOR_DELIVERY' => 4,
            'DELIVERED' => 5,
            'CANCELLED' => 0,
            default => 1,
        };
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function latestTrackingEvent()
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('event_time');
    }

    public function exceptions()
    {
        return $this->hasMany(ShipmentException::class)->latest('detected_at');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}

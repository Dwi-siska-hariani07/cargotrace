<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'city',
        'country',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getTimezoneAttribute(): string
    {
        return match (strtoupper($this->code)) {
            'IDJKT', 'IDCGK' => 'Asia/Jakarta',
            'SGSIN', 'SGSIN-AIR' => 'Asia/Singapore',
            'NLRTM' => 'Europe/Amsterdam',
            'DEHAM' => 'Europe/Berlin',
            'USLAX' => 'America/Los_Angeles',
            'USNYC' => 'America/New_York',
            'AUSYD' => 'Australia/Sydney',
            'AEDXB' => 'Asia/Dubai',
            'CNSHA' => 'Asia/Shanghai',
            default => 'UTC',
        };
    }

    public function outboundShipments()
    {
        return $this->hasMany(Shipment::class, 'origin_id');
    }

    public function inboundShipments()
    {
        return $this->hasMany(Shipment::class, 'destination_id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }
}

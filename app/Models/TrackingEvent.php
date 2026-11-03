<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'event_status',
        'location_id',
        'location_name',
        'latitude',
        'longitude',
        'event_time',
        'description',
        'is_exception',
        'created_by_user_id',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'is_exception' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

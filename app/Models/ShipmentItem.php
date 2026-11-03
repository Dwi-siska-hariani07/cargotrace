<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'item_name',
        'quantity',
        'package_type',
        'weight_kg',
        'volume_cbm',
        'hs_code',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'weight_kg' => 'float',
        'volume_cbm' => 'float',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}

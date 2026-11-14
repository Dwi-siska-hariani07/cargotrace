<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Carrier;
use App\Models\ShipmentException;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $shipmentQuery = Shipment::query();
        if ($user->isCustomer()) {
            $shipmentQuery->where('customer_id', $user->id);
        }

        $totalShipments = (clone $shipmentQuery)->count();
        $inTransitCount = (clone $shipmentQuery)->where('current_status', 'IN_TRANSIT')->count();
        $deliveredCount = (clone $shipmentQuery)->where('current_status', 'DELIVERED')->count();
        $delayedCount = (clone $shipmentQuery)->where('is_delayed', true)->count();

        $openExceptionsQuery = ShipmentException::where('status', '!=', 'RESOLVED');
        if ($user->isCustomer()) {
            $openExceptionsQuery->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
        $openExceptionsCount = $openExceptionsQuery->count();

        // Delay & Exception Rates
        $delayRate = $totalShipments > 0 ? round(($delayedCount / $totalShipments) * 100, 1) : 0;
        $exceptionRate = $totalShipments > 0 ? round(($openExceptionsCount / $totalShipments) * 100, 1) : 0;

        // Shipments by Status for Chart.js
        $statusCounts = (clone $shipmentQuery)
            ->selectRaw('current_status, count(*) as count')
            ->groupBy('current_status')
            ->pluck('count', 'current_status')
            ->toArray();

        // Recent shipments with relationships
        $recentShipments = (clone $shipmentQuery)
            ->with(['carrier', 'origin', 'destination', 'customer'])
            ->latest()
            ->take(6)
            ->get();

        // Shipments with active coordinates for global map view
        $mapShipments = (clone $shipmentQuery)
            ->whereNotNull('current_latitude')
            ->whereNotNull('current_longitude')
            ->with(['carrier', 'origin', 'destination'])
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'shipment_number' => $s->shipment_number,
                    'transport_type' => $s->transport_type,
                    'current_status' => $s->current_status,
                    'is_delayed' => $s->is_delayed,
                    'carrier' => $s->carrier->name ?? 'N/A',
                    'location_name' => $s->current_location_name ?? 'In Transit',
                    'latitude' => (float) $s->current_latitude,
                    'longitude' => (float) $s->current_longitude,
                    'origin' => $s->origin->name ?? ($s->origin->city ?? ''),
                    'destination' => $s->destination->name ?? ($s->destination->city ?? ''),
                    'origin_city' => $s->origin->city ?? ($s->origin->name ?? ''),
                    'destination_city' => $s->destination->city ?? ($s->destination->name ?? ''),
                    'origin_lat' => $s->origin ? (float) $s->origin->latitude : null,
                    'origin_lng' => $s->origin ? (float) $s->origin->longitude : null,
                    'dest_lat' => $s->destination ? (float) $s->destination->latitude : null,
                    'dest_lng' => $s->destination ? (float) $s->destination->longitude : null,
                    'show_url' => route('shipments.show', $s->id),
                ];
            });

        // Notifications
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalShipments',
            'inTransitCount',
            'deliveredCount',
            'delayedCount',
            'openExceptionsCount',
            'delayRate',
            'exceptionRate',
            'statusCounts',
            'recentShipments',
            'mapShipments',
            'notifications'
        ));
    }
}


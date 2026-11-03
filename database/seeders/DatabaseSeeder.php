<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\ShipmentException;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users (Admin, Operator, Customers)
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cargotrace.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+62 811 0000 001',
            'company' => 'CargoTrace Global HQ',
        ]);

        $operator = User::create([
            'name' => 'Budi Operator',
            'email' => 'operator@cargotrace.com',
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'phone' => '+62 812 0000 002',
            'company' => 'CargoTrace Hub Tanjung Priok',
        ]);

        $customer1 = User::create([
            'name' => 'PT Sumber Jaya Global',
            'email' => 'customer@cargotrace.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '+62 813 0000 003',
            'company' => 'PT Sumber Jaya Global',
        ]);

        $customer2 = User::create([
            'name' => 'Acme International Logistics',
            'email' => 'acme@corp.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '+1 415 555 2673',
            'company' => 'Acme Corp',
        ]);

        // 2. Create Carriers
        $maersk = Carrier::create([
            'code' => 'MAERSK',
            'name' => 'A.P. Moller - Maersk',
            'type' => 'SEA',
            'contact_email' => 'operations@maersk.com',
            'contact_phone' => '+45 33 63 33 63',
            'is_active' => true,
        ]);

        $evergreen = Carrier::create([
            'code' => 'EVERGREEN',
            'name' => 'Evergreen Marine Corp',
            'type' => 'SEA',
            'contact_email' => 'cargo@evergreen-marine.com',
            'contact_phone' => '+886 2 2501 1234',
            'is_active' => true,
        ]);

        $siaCargo = Carrier::create([
            'code' => 'SIA-CARGO',
            'name' => 'Singapore Airlines Cargo',
            'type' => 'AIR',
            'contact_email' => 'cargo@singaporeair.com.sg',
            'contact_phone' => '+65 6542 5588',
            'is_active' => true,
        ]);

        $fedex = Carrier::create([
            'code' => 'FEDEX',
            'name' => 'FedEx Express Global',
            'type' => 'AIR',
            'contact_email' => 'freight@fedex.com',
            'contact_phone' => '+1 800 463 3339',
            'is_active' => true,
        ]);

        // 3. Create Locations (With real coordinates for Leaflet maps)
        $locJkt = Location::create([
            'code' => 'IDJKT',
            'name' => 'Port of Tanjung Priok',
            'type' => 'SEAPORT',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
        ]);

        $locSin = Location::create([
            'code' => 'SGSIN',
            'name' => 'Port of Singapore',
            'type' => 'SEAPORT',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'latitude' => 1.2644000,
            'longitude' => 103.8228000,
        ]);

        $locRtm = Location::create([
            'code' => 'NLRTM',
            'name' => 'Port of Rotterdam',
            'type' => 'SEAPORT',
            'city' => 'Rotterdam',
            'country' => 'Netherlands',
            'latitude' => 51.9244000,
            'longitude' => 4.4777000,
        ]);

        $locLax = Location::create([
            'code' => 'USLAX',
            'name' => 'Port of Los Angeles',
            'type' => 'SEAPORT',
            'city' => 'Los Angeles',
            'country' => 'United States',
            'latitude' => 33.7432000,
            'longitude' => -118.2673000,
        ]);

        $locSyd = Location::create([
            'code' => 'AUSYD',
            'name' => 'Port of Sydney (Botany)',
            'type' => 'SEAPORT',
            'city' => 'Sydney',
            'country' => 'Australia',
            'latitude' => -33.9744000,
            'longitude' => 151.2183000,
        ]);

        $locCgkAir = Location::create([
            'code' => 'IDCGK',
            'name' => 'Soekarno-Hatta Air Freight Terminal',
            'type' => 'AIRPORT',
            'city' => 'Tangerang/Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.1275000,
            'longitude' => 106.6537000,
        ]);

        $locSinAir = Location::create([
            'code' => 'SGSIN-AIR',
            'name' => 'Changi Airfreight Centre',
            'type' => 'AIRPORT',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'latitude' => 1.3644000,
            'longitude' => 103.9915000,
        ]);

        // 4. Create Shipments

        // Shipment 1: Southeast Asia -> Europe (Delayed by customs hold)
        $now = Carbon::now();
        $s1 = Shipment::create([
            'shipment_number' => 'CRG-2026-8801',
            'customer_id' => $customer1->id,
            'carrier_id' => $maersk->id,
            'origin_id' => $locJkt->id,
            'destination_id' => $locRtm->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'Electronics & Microchips',
            'total_weight_kg' => 14250.00,
            'total_volume_cbm' => 58.40,
            'expected_departure' => $now->copy()->subDays(6),
            'expected_arrival' => $now->copy()->addDays(12),
            'estimated_arrival' => $now->copy()->addDays(14), // 48 hours delay!
            'actual_departure' => $now->copy()->subDays(6)->addHours(2),
            'current_status' => 'IN_TRANSIT',
            'is_delayed' => true,
            'delay_hours' => 48,
            'delay_severity' => 'HIGH',
            'delay_reason' => 'Vessel inspection & customs documentation hold at transit hub',
            'current_latitude' => 1.2644000,
            'current_longitude' => 103.8228000,
            'current_location_name' => 'Port of Singapore (Transit Anchor)',
            'notes' => 'Urgent industrial shipment under Letter of Credit.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s1->id,
            'item_name' => 'Microcontroller Semiconductor Units',
            'quantity' => 120,
            'package_type' => 'Pallet',
            'weight_kg' => 8400.00,
            'volume_cbm' => 32.00,
            'hs_code' => '8542.31.00',
        ]);
        ShipmentItem::create([
            'shipment_id' => $s1->id,
            'item_name' => 'PCB Motherboard Assemblies',
            'quantity' => 80,
            'package_type' => 'Pallet',
            'weight_kg' => 5850.00,
            'volume_cbm' => 26.40,
            'hs_code' => '8473.30.20',
        ]);

        // Tracking events for Shipment 1
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'CREATED',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(7),
            'description' => 'Shipment booking created and documentation submitted.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'BOOKED',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(6)->subHours(12),
            'description' => 'Vessel space confirmed on Maersk Mc-Kinney Moller V.2608.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok Container Terminal',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(6),
            'description' => 'Containers gated in and loaded onto vessel.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'IN_TRANSIT',
            'location_id' => $locSin->id,
            'location_name' => 'Port of Singapore',
            'latitude' => 1.2644000,
            'longitude' => 103.8228000,
            'event_time' => $now->copy()->subDays(4),
            'description' => 'Arrived at Singapore transshipment hub.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'CUSTOMS_HOLD',
            'location_id' => $locSin->id,
            'location_name' => 'Port of Singapore Customs Anchorage',
            'latitude' => 1.2644000,
            'longitude' => 103.8228000,
            'event_time' => $now->copy()->subDays(2),
            'description' => 'Held by customs inspection for import manifest verification.',
            'is_exception' => true,
            'created_by_user_id' => $operator->id,
        ]);

        // Exception on Shipment 1
        ShipmentException::create([
            'shipment_id' => $s1->id,
            'exception_type' => 'CUSTOMS_HOLD',
            'severity' => 'HIGH',
            'status' => 'OPEN',
            'description' => 'Discrepancy in declared HS Code on invoice vs physical container inspection.',
            'detected_at' => $now->copy()->subDays(2),
            'reported_by_user_id' => $operator->id,
        ]);

        // Shipment 2: Southeast Asia -> Australia (On Time, In Transit)
        $s2 = Shipment::create([
            'shipment_number' => 'CRG-2026-8802',
            'customer_id' => $customer1->id,
            'carrier_id' => $evergreen->id,
            'origin_id' => $locJkt->id,
            'destination_id' => $locSyd->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'Furniture & Home Decor',
            'total_weight_kg' => 9600.00,
            'total_volume_cbm' => 45.00,
            'expected_departure' => $now->copy()->subDays(3),
            'expected_arrival' => $now->copy()->addDays(7),
            'estimated_arrival' => $now->copy()->addDays(7), // On time
            'actual_departure' => $now->copy()->subDays(3)->addHour(),
            'current_status' => 'IN_TRANSIT',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => -14.2350,
            'current_longitude' => 125.1200,
            'current_location_name' => 'Timor Sea / North Australia Coast',
            'notes' => 'Standard dry container 40ft high cube.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s2->id,
            'item_name' => 'Teak Wood Dining Sets',
            'quantity' => 45,
            'package_type' => 'Carton',
            'weight_kg' => 9600.00,
            'volume_cbm' => 45.00,
            'hs_code' => '9403.60.10',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s2->id,
            'event_status' => 'CREATED',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(4),
            'description' => 'Shipment booked with Evergreen Line.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s2->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(3),
            'description' => 'Container loaded aboard vessel Ever Glory.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s2->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'Timor Sea',
            'latitude' => -14.2350,
            'longitude' => 125.1200,
            'event_time' => $now->copy()->subDay(),
            'description' => 'Cruising speed 18 knots, normal weather conditions.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Shipment 3: Jakarta -> Singapore (Air Freight, DELIVERED)
        $s3 = Shipment::create([
            'shipment_number' => 'CRG-2026-8803',
            'customer_id' => $customer2->id,
            'carrier_id' => $siaCargo->id,
            'origin_id' => $locCgkAir->id,
            'destination_id' => $locSinAir->id,
            'transport_type' => 'AIR',
            'cargo_type' => 'Medical Supplies & Vaccines',
            'total_weight_kg' => 850.00,
            'total_volume_cbm' => 4.20,
            'expected_departure' => $now->copy()->subDays(2),
            'expected_arrival' => $now->copy()->subDay(),
            'estimated_arrival' => $now->copy()->subDay(),
            'actual_departure' => $now->copy()->subDays(2),
            'actual_arrival' => $now->copy()->subDay(),
            'current_status' => 'DELIVERED',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => 1.3644000,
            'current_longitude' => 103.9915000,
            'current_location_name' => 'Changi Airfreight Centre (Destination Delivery)',
            'notes' => 'Temperature controlled cold-chain pharma shipment.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s3->id,
            'item_name' => 'Cold-Chain Diagnostic Test Kits',
            'quantity' => 10,
            'package_type' => 'Envirotainer RKN',
            'weight_kg' => 850.00,
            'volume_cbm' => 4.20,
            'hs_code' => '3002.15.00',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s3->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locCgkAir->id,
            'location_name' => 'CGK Air Cargo Terminal',
            'latitude' => -6.1275000,
            'longitude' => 106.6537000,
            'event_time' => $now->copy()->subDays(2),
            'description' => 'Received at CGK cold room and loaded into SQ715.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s3->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'En route to Singapore',
            'latitude' => 0.1200,
            'longitude' => 104.5000,
            'event_time' => $now->copy()->subDays(2)->addHours(2),
            'description' => 'Flight SQ715 en-route at cruising altitude 36,000 ft.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s3->id,
            'event_status' => 'DELIVERED',
            'location_id' => $locSinAir->id,
            'location_name' => 'Changi Airfreight Centre, Singapore',
            'latitude' => 1.3644000,
            'longitude' => 103.9915000,
            'event_time' => $now->copy()->subDay(),
            'description' => 'Customs cleared and hand-delivered to consignee representative. Signed by J. Tan.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Notifications
        Notification::create([
            'user_id' => $customer1->id,
            'shipment_id' => $s1->id,
            'title' => 'Customs Hold Alert on CRG-2026-8801',
            'message' => 'Shipment CRG-2026-8801 has encountered a Customs Hold in Singapore. New estimated arrival is delayed by 48 hours.',
            'type' => 'EXCEPTION_ALERT',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $customer2->id,
            'shipment_id' => $s3->id,
            'title' => 'Shipment Delivered: CRG-2026-8803',
            'message' => 'Your shipment CRG-2026-8803 has been successfully delivered in Singapore Changi Airport.',
            'type' => 'STATUS_CHANGE',
            'is_read' => true,
            'read_at' => $now->copy()->subHours(10),
        ]);

        // Audit Logs
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'SYSTEM_INIT',
            'entity_type' => 'System',
            'entity_id' => 1,
            'new_values' => ['message' => 'CargoTrace platform initialized with baseline seed data.'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder CLI',
        ]);
        AuditLog::create([
            'user_id' => $operator->id,
            'action' => 'LOG_EXCEPTION',
            'entity_type' => 'ShipmentException',
            'entity_id' => 1,
            'new_values' => [
                'shipment_number' => 'CRG-2026-8801',
                'exception_type' => 'CUSTOMS_HOLD',
                'severity' => 'HIGH'
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
    }
}

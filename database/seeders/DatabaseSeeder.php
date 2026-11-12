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
        $now = Carbon::now();

        // 1. Default Users (Admin, Operator, Corporate Customers)
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cargotrace.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+62 811 0000 001',
            'company' => 'CargoTrace Global HQ',
        ]);

        $operator = User::create([
            'name' => 'Budi Santoso',
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

        $customer3 = User::create([
            'name' => 'PT Nusantara Ekspor Mandiri',
            'email' => 'eksportir@nusantara.co.id',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '+62 31 5500 889',
            'company' => 'PT Nusantara Ekspor Mandiri',
        ]);

        // 2. Global Shipping & Multimodal Carriers
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

        $cosco = Carrier::create([
            'code' => 'COSCO',
            'name' => 'COSCO Shipping Lines',
            'type' => 'SEA',
            'contact_email' => 'dispatch@coscoshipping.com',
            'contact_phone' => '+86 21 3512 4888',
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

        $cargolux = Carrier::create([
            'code' => 'CARGOLUX',
            'name' => 'Cargolux Airlines Int.',
            'type' => 'AIR',
            'contact_email' => 'ops@cargolux.com',
            'contact_phone' => '+352 4211 1',
            'is_active' => true,
        ]);

        $lintasLand = Carrier::create([
            'code' => 'LINTAS-ID',
            'name' => 'Lintas Nusantara Express',
            'type' => 'LAND',
            'contact_email' => 'fleet@lintasnusantara.co.id',
            'contact_phone' => '+62 21 5088 7700',
            'is_active' => true,
        ]);

        // 3. International Ports, Airports & Distribution Hubs
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

        $locDxb = Location::create([
            'code' => 'AEDXB',
            'name' => 'Port of Jebel Ali (Dubai)',
            'type' => 'SEAPORT',
            'city' => 'Dubai',
            'country' => 'United Arab Emirates',
            'latitude' => 25.0113,
            'longitude' => 55.0617,
        ]);

        $locSha = Location::create([
            'code' => 'CNSHA',
            'name' => 'Port of Shanghai',
            'type' => 'SEAPORT',
            'city' => 'Shanghai',
            'country' => 'China',
            'latitude' => 31.2304,
            'longitude' => 121.4737,
        ]);

        $locNyc = Location::create([
            'code' => 'USNYC',
            'name' => 'Port of New York & New Jersey',
            'type' => 'SEAPORT',
            'city' => 'New York',
            'country' => 'United States',
            'latitude' => 40.6698,
            'longitude' => -74.1485,
        ]);

        $locSub = Location::create([
            'code' => 'IDSUB',
            'name' => 'Port of Tanjung Perak',
            'type' => 'SEAPORT',
            'city' => 'Surabaya',
            'country' => 'Indonesia',
            'latitude' => -7.2008,
            'longitude' => 112.7294,
        ]);

        $locYok = Location::create([
            'code' => 'JPYOK',
            'name' => 'Port of Yokohama',
            'type' => 'SEAPORT',
            'city' => 'Yokohama',
            'country' => 'Japan',
            'latitude' => 35.4437,
            'longitude' => 139.6380,
        ]);

        // 4. Global Multimodal Shipments (9 Active & Historical Routes)

        // Shipment 1: Jakarta -> Rotterdam (Delayed by customs hold)
        $s1 = Shipment::create([
            'shipment_number' => 'CRG-2026-8801',
            'customer_id' => $customer1->id,
            'carrier_id' => $maersk->id,
            'origin_id' => $locJkt->id,
            'destination_id' => $locRtm->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'Electronics & Microchips',
            'vessel_or_flight' => 'MV Maersk Mc-Kinney Moller V.2608',
            'container_or_awb' => "MSKU 904281-4 (40' HC)",
            'receiver_name' => 'EuroTech Semiconductors BV (Rotterdam)',
            'total_weight_kg' => 14250.00,
            'total_volume_cbm' => 58.40,
            'expected_departure' => $now->copy()->subDays(6),
            'expected_arrival' => $now->copy()->addDays(12),
            'estimated_arrival' => $now->copy()->addDays(14),
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

        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'CREATED',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(7),
            'description' => 'Shipment booking created and export PEB documentation submitted.',
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
            'description' => 'Vessel space confirmed on MV Maersk Mc-Kinney Moller V.2608.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s1->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok JICT Terminal 1',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(6),
            'description' => 'Container MSKU 904281-4 gated in and loaded onto vessel.',
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
            'description' => 'Arrived at PSA Singapore transshipment terminal.',
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

        ShipmentException::create([
            'shipment_id' => $s1->id,
            'exception_type' => 'CUSTOMS_HOLD',
            'severity' => 'HIGH',
            'status' => 'OPEN',
            'description' => 'Discrepancy in declared HS Code on invoice vs physical container inspection.',
            'detected_at' => $now->copy()->subDays(2),
            'reported_by_user_id' => $operator->id,
        ]);

        // Shipment 2: Jakarta -> Sydney (On Time, In Transit)
        $s2 = Shipment::create([
            'shipment_number' => 'CRG-2026-8802',
            'customer_id' => $customer1->id,
            'carrier_id' => $evergreen->id,
            'origin_id' => $locJkt->id,
            'destination_id' => $locSyd->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'Furniture & Home Decor',
            'vessel_or_flight' => 'MV Ever Glory V.142E',
            'container_or_awb' => "EGHU 772190-8 (40' HC)",
            'receiver_name' => 'Sydney Living & Timber Pty Ltd',
            'total_weight_kg' => 9600.00,
            'total_volume_cbm' => 45.00,
            'expected_departure' => $now->copy()->subDays(3),
            'expected_arrival' => $now->copy()->addDays(7),
            'estimated_arrival' => $now->copy()->addDays(7),
            'actual_departure' => $now->copy()->subDays(3)->addHour(),
            'current_status' => 'IN_TRANSIT',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => -14.2350,
            'current_longitude' => 125.1200,
            'current_location_name' => 'Timor Sea / North Australia Coast',
            'notes' => 'Standard dry container 40ft high cube with timber fumigation certificate.',
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
            'description' => 'Shipment booked with Evergreen Marine Corp.',
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
            'description' => 'Container loaded aboard MV Ever Glory.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s2->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'Timor Sea Corridor',
            'latitude' => -14.2350,
            'longitude' => 125.1200,
            'event_time' => $now->copy()->subDay(),
            'description' => 'Cruising speed 18.4 knots, normal sea conditions.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Shipment 3: Jakarta Air -> Singapore Air (DELIVERED)
        $s3 = Shipment::create([
            'shipment_number' => 'CRG-2026-8803',
            'customer_id' => $customer2->id,
            'carrier_id' => $siaCargo->id,
            'origin_id' => $locCgkAir->id,
            'destination_id' => $locSinAir->id,
            'transport_type' => 'AIR',
            'cargo_type' => 'Medical Supplies & Vaccines',
            'vessel_or_flight' => 'Flight SQ715 Freighter',
            'container_or_awb' => 'AWB 618-44901284',
            'receiver_name' => 'Mount Elizabeth Pharma Hub (Signed: J. Tan)',
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
            'notes' => 'Temperature controlled 2-8C cold-chain pharma shipment.',
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
            'description' => 'Received at CGK cold room and loaded onto SQ715.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s3->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'Batam Airspace Corridor',
            'latitude' => 0.1200,
            'longitude' => 104.5000,
            'event_time' => $now->copy()->subDays(2)->addHours(2),
            'description' => 'Flight SQ715 cruising at altitude 36,000 ft.',
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

        // Shipment 4: Shanghai -> Los Angeles (Sea, Weather Swell Delay +18h)
        $s4 = Shipment::create([
            'shipment_number' => 'CRG-2026-8804',
            'customer_id' => $customer2->id,
            'carrier_id' => $cosco->id,
            'origin_id' => $locSha->id,
            'destination_id' => $locLax->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'EV Lithium Battery Modules',
            'vessel_or_flight' => 'MV COSCO Shipping Universe V.091E',
            'container_or_awb' => "CSNU 618204-7 (40' Reefer)",
            'receiver_name' => 'Pacific Mobility Assembly Inc. (Long Beach)',
            'total_weight_kg' => 18400.00,
            'total_volume_cbm' => 64.20,
            'expected_departure' => $now->copy()->subDays(9),
            'expected_arrival' => $now->copy()->addDays(6),
            'estimated_arrival' => $now->copy()->addDays(6)->addHours(18),
            'actual_departure' => $now->copy()->subDays(9),
            'current_status' => 'IN_TRANSIT',
            'is_delayed' => true,
            'delay_hours' => 18,
            'delay_severity' => 'MEDIUM',
            'delay_reason' => 'North Pacific severe winter gale rerouting south of Aleutian chain',
            'current_latitude' => 38.4500,
            'current_longitude' => -162.3000,
            'current_location_name' => 'North Pacific Ocean Waypoint',
            'notes' => 'DG Class 9 HAZMAT Lithium packing certified.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s4->id,
            'item_name' => 'LFP 48V Commercial Battery Packs',
            'quantity' => 64,
            'package_type' => 'DG Steel Crate',
            'weight_kg' => 18400.00,
            'volume_cbm' => 64.20,
            'hs_code' => '8507.60.00',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s4->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locSha->id,
            'location_name' => 'Yangshan Automated Terminal, Shanghai',
            'latitude' => 31.2304,
            'longitude' => 121.4737,
            'event_time' => $now->copy()->subDays(9),
            'description' => 'Cleared Yangshan Terminal 4 automated crane gate-in.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s4->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'North Pacific Ocean Waypoint',
            'latitude' => 38.4500,
            'longitude' => -162.3000,
            'event_time' => $now->copy()->subHours(8),
            'description' => 'Vessel diverted 85 nm south due to 6m swell system; ETA adjusted +18h.',
            'is_exception' => true,
            'created_by_user_id' => $operator->id,
        ]);

        ShipmentException::create([
            'shipment_id' => $s4->id,
            'exception_type' => 'WEATHER_DELAY',
            'severity' => 'MEDIUM',
            'status' => 'OPEN',
            'description' => 'Severe North Pacific low-pressure gale causing conservative speed reduction.',
            'detected_at' => $now->copy()->subHours(8),
            'reported_by_user_id' => $operator->id,
        ]);

        // Shipment 5: Jakarta -> Dubai (Sea, OUT_FOR_DELIVERY / Port Clearance)
        $s5 = Shipment::create([
            'shipment_number' => 'CRG-2026-8805',
            'customer_id' => $customer3->id,
            'carrier_id' => $maersk->id,
            'origin_id' => $locJkt->id,
            'destination_id' => $locDxb->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'Specialty Gayo Arabica Coffee',
            'vessel_or_flight' => 'MV Maersk Elba V.2611W',
            'container_or_awb' => "MSKU 451092-1 (20' Dry)",
            'receiver_name' => 'Al-Futtaim Roastery & Commodities LLC',
            'total_weight_kg' => 11200.00,
            'total_volume_cbm' => 28.50,
            'expected_departure' => $now->copy()->subDays(11),
            'expected_arrival' => $now->copy()->addHours(16),
            'estimated_arrival' => $now->copy()->addHours(16),
            'actual_departure' => $now->copy()->subDays(11),
            'current_status' => 'OUT_FOR_DELIVERY',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => 25.0113,
            'current_longitude' => 55.0617,
            'current_location_name' => 'Jebel Ali Container Terminal 3 (Dubai)',
            'notes' => 'Halal & phytosanitary export certificate verified at destination.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s5->id,
            'item_name' => 'Grade-1 Green Arabica Coffee Sacks (60kg)',
            'quantity' => 185,
            'package_type' => 'Jute Bag',
            'weight_kg' => 11200.00,
            'volume_cbm' => 28.50,
            'hs_code' => '0901.11.10',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s5->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locJkt->id,
            'location_name' => 'Port of Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDays(11),
            'description' => 'Export container sealed and loaded at Tanjung Priok.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s5->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'Arabian Sea Maritime Lane',
            'latitude' => 14.8000,
            'longitude' => 64.5000,
            'event_time' => $now->copy()->subDays(4),
            'description' => 'Cleared Indian Ocean transit corridor on schedule.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s5->id,
            'event_status' => 'OUT_FOR_DELIVERY',
            'location_id' => $locDxb->id,
            'location_name' => 'DP World Jebel Ali Terminal 3, Dubai',
            'latitude' => 25.0113,
            'longitude' => 55.0617,
            'event_time' => $now->copy()->subHours(4),
            'description' => 'Dubai Customs cleared; container loaded on final-mile drayage truck.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Shipment 6: New York -> Rotterdam (Air Freight, In Transit)
        $s6 = Shipment::create([
            'shipment_number' => 'CRG-2026-8806',
            'customer_id' => $customer2->id,
            'carrier_id' => $fedex->id,
            'origin_id' => $locNyc->id,
            'destination_id' => $locRtm->id,
            'transport_type' => 'AIR',
            'cargo_type' => 'Aerospace Turbine Sensors',
            'vessel_or_flight' => 'Flight FX5018 B777F',
            'container_or_awb' => 'AWB 023-88912041',
            'receiver_name' => 'EuroAero Precision Maintenance (Schiphol/RTM)',
            'total_weight_kg' => 1420.00,
            'total_volume_cbm' => 6.80,
            'expected_departure' => $now->copy()->subHours(10),
            'expected_arrival' => $now->copy()->addHours(14),
            'estimated_arrival' => $now->copy()->addHours(14),
            'actual_departure' => $now->copy()->subHours(10),
            'current_status' => 'IN_TRANSIT',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => 48.6200,
            'current_longitude' => -34.1500,
            'current_location_name' => 'North Atlantic Air Track NAT-C',
            'notes' => 'Priority AOG (Aircraft On Ground) aerospace parts.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s6->id,
            'item_name' => 'Titanium Engine Vibration Transducers',
            'quantity' => 24,
            'package_type' => 'Shockproof Pelican Case',
            'weight_kg' => 1420.00,
            'volume_cbm' => 6.80,
            'hs_code' => '9031.80.80',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s6->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locNyc->id,
            'location_name' => 'Newark / NY Air Cargo Hub',
            'latitude' => 40.6698,
            'longitude' => -74.1485,
            'event_time' => $now->copy()->subHours(10),
            'description' => 'Cleared TSA air freight screening and departed on FX5018.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s6->id,
            'event_status' => 'IN_TRANSIT',
            'location_name' => 'North Atlantic Track NAT-C',
            'latitude' => 48.6200,
            'longitude' => -34.1500,
            'event_time' => $now->copy()->subHours(3),
            'description' => 'Flight FX5018 crossing Atlantic corridor FL370 on schedule.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Shipment 7: Surabaya -> Jakarta (Overland Trucking, DELIVERED)
        $s7 = Shipment::create([
            'shipment_number' => 'CRG-2026-8807',
            'customer_id' => $customer3->id,
            'carrier_id' => $lintasLand->id,
            'origin_id' => $locSub->id,
            'destination_id' => $locJkt->id,
            'transport_type' => 'LAND',
            'cargo_type' => 'Packaged FMCG & Retail Goods',
            'vessel_or_flight' => 'Tronton Wingbox B 9421 TXU',
            'container_or_awb' => 'DO-LNS-2026-7712',
            'receiver_name' => 'Sentral Distribusi Priok (Signed: R. Pratama)',
            'total_weight_kg' => 12400.00,
            'total_volume_cbm' => 42.00,
            'expected_departure' => $now->copy()->subDays(2),
            'expected_arrival' => $now->copy()->subDay(),
            'estimated_arrival' => $now->copy()->subDay(),
            'actual_departure' => $now->copy()->subDays(2),
            'actual_arrival' => $now->copy()->subDay(),
            'current_status' => 'DELIVERED',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => -6.1038190,
            'current_longitude' => 106.8821940,
            'current_location_name' => 'Warehouse Hub Tanjung Priok, Jakarta',
            'notes' => 'Trans-Java Toll Corridor express domestic linehaul.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s7->id,
            'item_name' => 'Beverage Tetra-Pak Retail Pallets',
            'quantity' => 32,
            'package_type' => 'Euro Pallet',
            'weight_kg' => 12400.00,
            'volume_cbm' => 42.00,
            'hs_code' => '2202.99.90',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s7->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locSub->id,
            'location_name' => 'Tanjung Perak Logistics Hub, Surabaya',
            'latitude' => -7.2008,
            'longitude' => 112.7294,
            'event_time' => $now->copy()->subDays(2),
            'description' => 'Dispatched from Surabaya consolidation warehouse via Trans-Java Toll.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s7->id,
            'event_status' => 'DELIVERED',
            'location_id' => $locJkt->id,
            'location_name' => 'Warehouse Hub Tanjung Priok, Jakarta',
            'latitude' => -6.1038190,
            'longitude' => 106.8821940,
            'event_time' => $now->copy()->subDay(),
            'description' => 'Unloaded intact; e-POD signed by R. Pratama.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Shipment 8: Yokohama -> Jakarta (Sea, PICKED_UP at Origin Port)
        $s8 = Shipment::create([
            'shipment_number' => 'CRG-2026-8808',
            'customer_id' => $customer1->id,
            'carrier_id' => $cosco->id,
            'origin_id' => $locYok->id,
            'destination_id' => $locJkt->id,
            'transport_type' => 'SEA',
            'cargo_type' => 'Precision CNC Robotics Arms',
            'vessel_or_flight' => 'MV COSCO Faith V.034S',
            'container_or_awb' => "CSNU 883910-2 (40' Flat Rack)",
            'receiver_name' => 'PT Astra Manufaktur Presisi (Karawang)',
            'total_weight_kg' => 16800.00,
            'total_volume_cbm' => 52.00,
            'expected_departure' => $now->copy()->addHours(4),
            'expected_arrival' => $now->copy()->addDays(11),
            'estimated_arrival' => $now->copy()->addDays(11),
            'current_status' => 'PICKED_UP',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => 35.4437,
            'current_longitude' => 139.6380,
            'current_location_name' => 'Port of Yokohama Minami Honmoku Berth',
            'notes' => 'Heavy industrial machinery with shock & tilt loggers attached.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s8->id,
            'item_name' => '6-Axis Automotive Assembly Robots',
            'quantity' => 6,
            'package_type' => 'Vacuum Wooden Case',
            'weight_kg' => 16800.00,
            'volume_cbm' => 52.00,
            'hs_code' => '8479.50.00',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s8->id,
            'event_status' => 'CREATED',
            'location_id' => $locYok->id,
            'location_name' => 'Port of Yokohama, Japan',
            'latitude' => 35.4437,
            'longitude' => 139.6380,
            'event_time' => $now->copy()->subDays(2),
            'description' => 'Booking confirmed and heavy-lift berth reserved.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s8->id,
            'event_status' => 'PICKED_UP',
            'location_id' => $locYok->id,
            'location_name' => 'Yokohama Minami Honmoku Terminal',
            'latitude' => 35.4437,
            'longitude' => 139.6380,
            'event_time' => $now->copy()->subHours(5),
            'description' => 'Flat-rack container loaded aboard MV COSCO Faith awaiting departure tide.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // Shipment 9: Singapore Air -> Jakarta Air (Air Freight, BOOKED)
        $s9 = Shipment::create([
            'shipment_number' => 'CRG-2026-8809',
            'customer_id' => $customer3->id,
            'carrier_id' => $cargolux->id,
            'origin_id' => $locSinAir->id,
            'destination_id' => $locCgkAir->id,
            'transport_type' => 'AIR',
            'cargo_type' => 'Fiber Optic Network Transceivers',
            'vessel_or_flight' => 'Flight CV794 Freighter',
            'container_or_awb' => 'AWB 172-55410982',
            'receiver_name' => 'PT Telkom Infrastruktur Nusantara',
            'total_weight_kg' => 620.00,
            'total_volume_cbm' => 3.40,
            'expected_departure' => $now->copy()->addHours(12),
            'expected_arrival' => $now->copy()->addDays(1)->addHours(4),
            'estimated_arrival' => $now->copy()->addDays(1)->addHours(4),
            'current_status' => 'BOOKED',
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_latitude' => 1.3644000,
            'current_longitude' => 103.9915000,
            'current_location_name' => 'Changi Airfreight Centre Staging Gate',
            'notes' => 'Scheduled overnight uplift on Cargolux 747-8F.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $s9->id,
            'item_name' => '400G QSFP-DD Optical Modules',
            'quantity' => 40,
            'package_type' => 'Anti-Static Carton',
            'weight_kg' => 620.00,
            'volume_cbm' => 3.40,
            'hs_code' => '8517.62.21',
        ]);

        TrackingEvent::create([
            'shipment_id' => $s9->id,
            'event_status' => 'CREATED',
            'location_id' => $locSinAir->id,
            'location_name' => 'Changi Airfreight Centre, Singapore',
            'latitude' => 1.3644000,
            'longitude' => 103.9915000,
            'event_time' => $now->copy()->subHours(6),
            'description' => 'Air Waybill 172-55410982 issued and cargo booked.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $s9->id,
            'event_status' => 'BOOKED',
            'location_id' => $locSinAir->id,
            'location_name' => 'Changi Airfreight Centre Staging Gate',
            'latitude' => 1.3644000,
            'longitude' => 103.9915000,
            'event_time' => $now->copy()->subHours(2),
            'description' => 'ULD PMC pallet confirmed for Flight CV794 uplift.',
            'is_exception' => false,
            'created_by_user_id' => $operator->id,
        ]);

        // 5. Customer & Operational Notifications
        Notification::create([
            'user_id' => $customer1->id,
            'shipment_id' => $s1->id,
            'title' => 'Customs Hold Alert on CRG-2026-8801',
            'message' => 'Shipment CRG-2026-8801 encountered a Customs Hold in Singapore. Estimated arrival adjusted +48 hours.',
            'type' => 'EXCEPTION_ALERT',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $customer2->id,
            'shipment_id' => $s4->id,
            'title' => 'Weather Reroute Advisory: CRG-2026-8804',
            'message' => 'MV COSCO Shipping Universe rerouted around North Pacific swell; arrival adjusted +18 hours.',
            'type' => 'EXCEPTION_ALERT',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $customer2->id,
            'shipment_id' => $s3->id,
            'title' => 'Shipment Delivered: CRG-2026-8803',
            'message' => 'Your shipment CRG-2026-8803 has been successfully delivered at Changi Airfreight Centre (Signed: J. Tan).',
            'type' => 'STATUS_CHANGE',
            'is_read' => true,
            'read_at' => $now->copy()->subHours(10),
        ]);

        // 6. Audit Logs
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'SYSTEM_INIT',
            'entity_type' => 'System',
            'entity_id' => 1,
            'new_values' => ['message' => 'CargoTrace global logistics baseline seeded across 12 ports and 9 active corridors.'],
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
                'severity' => 'HIGH',
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
    }
}

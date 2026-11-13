# CargoTrace — Global Shipment Visibility & Tracking Platform

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)
[![Automated Tests](https://img.shields.io/badge/PHPUnit-24%20Tests%20%7C%2085%20Assertions%20Passed-brightgreen.svg)]()
[![Bilingual UI](https://img.shields.io/badge/Language-ID%20%7C%20EN%20Bilingual-teal.svg)]()

> **Final Project (UAS) — Web Programming 2**  
> **CargoTrace** is an enterprise-grade global freight visibility and multimodal tracking platform built with Laravel 11, Tailwind CSS, Leaflet.js, and the Open-Meteo Marine Weather API. It provides real-time geospatial tracking, deterministic delay calculations, official electronic bills of lading (E-B/L) with QR verification, commercial freight quoting, and multi-tenant role-based access control.

---

## 📌 Problem & Enterprise Solution

### The Problem
* **Fragmented Supply Chain Visibility:** Trans-oceanic and air cargo movements pass through multiple carriers and transshipment hubs with disjointed tracking portals.
* **Delayed Disruption Awareness:** Shippers and consignees are often uninformed of port congestion, customs inspections, and weather anomalies until after delivery deadlines pass.
* **Commercial Document Privacy Vulnerabilities:** Public tracking portals frequently fail to separate open fleet telemetry from legally sensitive freight contracts and manifests (*Bills of Lading*).

### The CargoTrace Solution
* **Multi-Role Tenancy Architecture:** Tailored, secure experiences for Public Guests, Cargo Clients (*Customers*), Hub Dispatchers (*Operators*), and System Administrators.
* **Open Fleet Tracking vs. Legal Document Protection:** Guests can openly monitor vessel routes, GIS telemetry, and ETA countdowns; however, official *Electronic Bills of Lading (E-B/L)* and *e-POD* signatures are strictly restricted to verified cargo owners and authorized staff.
* **Deterministic State Machine:** Status strictly transitions across `CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` &rarr; `OUT_FOR_DELIVERY` &rarr; `DELIVERED`, disallowing backwards or invalid state jumps.
* **Automated Delay Engine:** Real-time variance evaluation between scheduled and estimated arrivals, categorizing severity into `LOW` (1–6h), `MEDIUM` (7–24h), and `HIGH` (>24h).
* **Multi-Layer GIS Satellite Mapping:** Integrated Leaflet.js with ArcGIS World Imagery satellite tiles, political boundary/city labels, and CartoDB Dark marine radar modes.
* **Live Atmospheric Matrix:** External integration with the Open-Meteo Weather API using Laravel's resilient HTTP client to pull real-time weather and wind telemetry for origin, mid-ocean coordinates, and destination hubs.
* **Commercial Freight Estimator:** Interactive instant quoting engine powered by the Haversine spherical distance formula across connected global sea and air hubs.
* **Full Bilingual Support:** Instant toggle between Bahasa Indonesia (🇮🇩) and English (🇬🇧) across all public, operational, and administrative views.
* **REST API Suite:** 18 documented endpoints authenticated via Laravel Sanctum tokens with automated Postman collection.

---

## 🧭 Course Syllabus Scenario Alignment (Group A, B, & C)

CargoTrace comprehensively models and satisfies all three case study configurations outlined in the course module:

| Group Scenario | Geographical Corridor | Delay Threshold | Primary Exception Type | Sample Seeded Consignment |
| :--- | :--- | :---: | :--- | :--- |
| **Group A** | Southeast Asia &rarr; Europe | 12 Hours | Customs Inspection & Documentation Hold | `CRG-2026-8801` (Jakarta &rarr; Rotterdam) |
| **Group B** | East Asia &rarr; North America | 18 Hours | Severe Pacific Maritime Storm / Weather Delay | `CRG-2026-8804` (Shanghai &rarr; Los Angeles) |
| **Group C** | Southeast Asia &rarr; Australia | 24 Hours | Export Manifest HS Code Discrepancy | `CRG-2026-8802` (Jakarta &rarr; Sydney) |

---

## 🏗️ System Architecture & Service Layer

CargoTrace adheres to clean enterprise architectural layering:

```
[ Browser Client / Postman REST API ]
                  │
                  ▼
[ HTTP Kernel & Middleware Layer ]
  ├── SetLocale (Bilingual ID/EN Session)
  ├── Authenticate (Sanctum / Session Guard)
  └── CheckRole (Admin, Operator, Customer Tenancy)
                  │
                  ▼
[ Controller Layer (Web & API) ]
  ├── ShipmentWebController / ShipmentApiController
  ├── TrackingWebController / ExceptionWebController
  └── DashboardController / UserWebController
                  │
                  ▼
[ Domain Service Layer ]
  ├── ShipmentService   (Lifecycle state machine, validation & audit trail)
  ├── TrackingService   (Chronological events, GPS coordinates & spatial update)
  ├── EtaService        (Deterministic delay classification & threshold calculation)
  ├── ExceptionService  (Disruption reporting, resolution & ETA re-computation)
  └── WeatherService    (Open-Meteo external atmospheric integration with fallback)
                  │
                  ▼
[ Eloquent ORM Models & MySQL Database ]
```

---

## 🗄️ Database Entity Relationship (ERD)

CargoTrace implements 9 relational tables:

1. `users` — Multi-tenant accounts and role definitions (`admin`, `operator`, `customer`).
2. `carriers` — Global shipping lines, airlines, and overland fleets (`MAERSK`, `EVERGREEN`, `COSCO`, `SIA-CARGO`, `FEDEX`, `CARGOLUX`, `LINTAS-ID`).
3. `locations` — Connected international seaports and airport hubs with precise GIS coordinates (`IDJKT`, `SGSIN`, `NLRTM`, `USLAX`, `AUSYD`, `IDCGK`, `AEDXB`, `CNSHA`, `USNYC`, `IDSUB`, `JPYOK`).
4. `shipments` — Core multimodal consignments containing vessel/flight names, container/AWB references, consignee metadata, weight, volume, scheduled ETAs, and delay flags.
5. `shipment_items` — Itemized manifests with customs HS classification codes and packages.
6. `tracking_events` — Immutable chronological milestones with timestamps and spatial coordinates.
7. `shipment_exceptions` — Operational disruption records (`CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, `DOCUMENTATION_ISSUE`).
8. `notifications` — Real-time in-app alerts for status milestones and delay notifications.
9. `audit_logs` — Forensic security trail recording actors, timestamped actions, entities, and IP addresses.

---

## 👥 Seeded Demo Accounts

You can sign in with any of these pre-seeded accounts:

| Role | Email Address | Password | Capabilities |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | `password123` | Complete system control, user & role management, carrier & hub CRUD, forensic audit log inspection |
| **Logistics Operator** | `operator@cargotrace.com` | `password123` | Register new shipments, record GPS tracking checkpoints, file & resolve operational disruptions |
| **Customer (Client 1)** | `customer@cargotrace.com` | `password123` | PT Sumber Jaya Global: View private consignments, track real-time map, print authorized Bills of Lading |
| **Customer (Client 2)** | `acme@corp.com` | `password123` | Acme Corp: Multi-tenant isolated shipments & medical cargo waybills |

---

## 🚀 Installation & Local Setup

### Prerequisites
* PHP 8.2 or higher
* MySQL 8.0+ / MariaDB (XAMPP recommended)
* Composer 2.x

### 1. Clone & Configure
```bash
git clone https://github.com/your-username/cargotrace.git
cd cargotrace

# Install PHP dependencies
composer install

# Environment configuration
cp .env.example .env
php artisan key:generate
```

### 2. Configure Database (`.env`)
Ensure MySQL is running (e.g., via XAMPP Control Panel) and verify `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cargotrace
DB_USERNAME=root
DB_PASSWORD=
DB_COLLATION=utf8mb4_unicode_ci
```

### 3. Run Migrations & Seeders
```bash
# Execute schema migrations and populate 9 realistic global shipments
php artisan migrate:fresh --seed
```

### 4. Start Local Development Server
```bash
php artisan serve
```
Access the application at **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

---

## 🧪 Automated Testing Suite

CargoTrace features a comprehensive automated test suite guaranteeing state machine integrity, deterministic delay calculations, API tenancy checks, and authorization boundaries:

```bash
# Run all tests via PHPUnit
php artisan test
```

### Test Results
```text
PHPUnit 10.5.66 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.2.12
Configuration: D:\cargotrace\phpunit.xml

........................                                          24 / 24 (100%)

Time: 00:22.632, Memory: 42.00 MB
OK (24 tests, 85 assertions)
```

### Test Coverage Highlights:
* `EtaServiceTest`: Validates deterministic delay calculation rules across all thresholds (`NONE`, `LOW`, `MEDIUM`, `HIGH`).
* `ShipmentLifecycleTest`: Verifies sequential state progression (`CREATED` &rarr; `IN_TRANSIT` &rarr; `DELIVERED`) and disallows illegal regression.
* `ApiShipmentTest`: Verifies Sanctum token authentication, 401 denials, and tenant isolation so customers cannot access rival consignments.
* `WebNavigationTest`: Validates bilingual locale switching, public guest restriction on legal documents, waybill access authorization (200 for owner, 403 for other customers), and quote estimator rendering.

---

## 📡 REST API Documentation

A complete Postman collection is provided in the repository root:
* **File:** `cargotrace_postman_collection.json`
* **Import:** Open Postman &rarr; Click **Import** &rarr; Select `cargotrace_postman_collection.json`.

### Core API Endpoints:
* `POST /api/login` & `POST /api/register`
* `GET /api/shipments` (with server-side pagination, search & filters)
* `POST /api/shipments` (Operator/Admin restricted)
* `GET /api/shipments/{id}` (Tenancy-checked)
* `GET /api/shipments/{id}/tracking` & `POST /api/shipments/{id}/tracking`
* `GET /api/carriers` & `POST /api/carriers`
* `GET /api/locations` & `POST /api/locations`
* `POST /api/shipments/{id}/exceptions` & `PATCH /api/exceptions/{id}`

---

## 📜 Attribution & Technologies
* **Framework:** [Laravel 11](https://laravel.com)
* **Styling & UI:** [Tailwind CSS](https://tailwindcss.com) & [Phosphor Icons](https://phosphoricons.com)
* **Mapping:** [Leaflet.js](https://leafletjs.com) & [ArcGIS World Imagery](https://www.esri.com)
* **Charts:** [Chart.js](https://www.chartjs.org)
* **Weather Telemetry:** [Open-Meteo Marine Weather API](https://open-meteo.com)
* **Barcode & QR Generator:** [qrcode-generator](https://github.com/kazuhikoarase/qrcode-generator)

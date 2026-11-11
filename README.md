# CargoTrace — Global Shipment Visibility & Tracking Platform

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)
[![Tests Passing](https://img.shields.io/badge/Automated%20Tests-21%20Passed-brightgreen.svg)]()

> Final Project (UAS) — Web Programming 2  
> **CargoTrace** is a production-grade portfolio project delivering global cargo tracking, deterministic delay calculations, chronological spatial timelines, exception management, and REST APIs.

---

## 📌 Problem & Solution Overview

### The Problem
* Freight and maritime shipments traverse complex international legs with fragmented status visibility.
* Customers lack real-time geographical progress, remaining unaware of ETA deviations until late.
* Customs holds, bad weather, and port congestion often disrupt supply chains without centralized tracking.

### The CargoTrace Solution
* **Multi-Role Visibility:** Tailored experiences for Customers, Logistics Operators, and Administrators.
* **Deterministic State Machine:** Status progresses strictly across `CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` &rarr; `OUT_FOR_DELIVERY` &rarr; `DELIVERED`, preventing invalid backwards transitions.
* **Automated Delay Rules:** Real-time variance evaluation between Expected Arrival and Estimated Arrival, classifying severity into `LOW` (1-6h), `MEDIUM` (7-24h), and `HIGH` (>24h).
* **Interactive GIS Mapping:** Integrated Leaflet.js with OpenStreetMap tiles displaying Origin, Destination, Waypoint Checkpoints, and polyline route arcs.
* **Real-time Atmospheric API:** Integrated with Open-Meteo API using Laravel HTTP Client to display live temperatures, wind speeds, and nautical weather conditions.
* **Full REST API Suite:** 18 documented endpoints authenticated via Laravel Sanctum tokens with automated Postman collection.

---

## 🏗️ System Architecture & Service Layer

CargoTrace adheres to clean enterprise architectural layering:

```
[ Browser / Postman Client ]
          │
          ▼
[ HTTP Routes & Middleware (Auth, Roles) ]
          │
          ▼
[ Controllers (Web & Api) ]
          │
          ▼
[ Domain Service Layer ]
  ├── ShipmentService   (Lifecycle state machine, validation & audit trail)
  ├── TrackingService   (Chronological events, GPS coordinates & spatial update)
  ├── EtaService        (Deterministic delay & threshold calculation)
  ├── ExceptionService  (Disruption reporting, incident resolution)
  └── WeatherService    (Open-Meteo external atmospheric integration with fallback)
          │
          ▼
[ Eloquent ORM Models & MySQL Database ]
```

---

## 🗄️ Database Entity Relationship (ERD)

CargoTrace implements 9 relational tables:

1. `users` — Authentication, multi-tenant roles (`admin`, `operator`, `customer`).
2. `carriers` — Shipping lines, airlines, and logistics operators (`MAERSK`, `EVERGREEN`, `SIA-CARGO`, `FEDEX`).
3. `locations` — Seaports, airports, and hubs with exact GIS coordinates (`IDJKT`, `SGSIN`, `NLRTM`, `USLAX`, `AUSYD`).
4. `shipments` — Core cargo entity with weights, volumes, scheduled ETAs, delay flags, and soft-delete support.
5. `shipment_items` — Itemized cargo manifests (HS codes, quantities, package types).
6. `tracking_events` — Immutable chronological checkpoints with timestamps and coordinates.
7. `shipment_exceptions` — Incident records (`CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, `DAMAGED`, `MISROUTED`).
8. `notifications` — Role-based in-app alerts for status updates and delays.
9. `audit_logs` — Comprehensive security audit trail logging actors, IP addresses, and state changes.

---

## 👥 Seeded Demo Accounts

You can sign in with any of these pre-seeded accounts (or use the one-click demo buttons on the `/login` page):

| Role | Email Address | Password | Capabilities |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | `password123` | Master control, user & role management, carriers & locations CRUD, audit log inspection |
| **Logistics Operator** | `operator@cargotrace.com` | `password123` | Create shipments, record tracking checkpoints with coordinates, log disruptions |
| **Customer** | `customer@cargotrace.com` | `password123` | View own cargo, real-time map tracking, in-app notifications & ETA notices |
| **Customer (US)** | `acme@corp.com` | `password123` | Multi-tenant customer scenario |

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
Ensure your MySQL service is started (e.g. via XAMPP) and verify database settings in `.env`:
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
# Execute schema migration and populate realistic sample data
php artisan migrate:fresh --seed
```

### 4. Start Local Development Server
```bash
php artisan serve
```
Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## 🧪 Automated Testing Suite

CargoTrace includes comprehensive unit and feature tests covering authentication, authorization, shipment lifecycle state transitions, and delay calculations:

```bash
# Run all tests
php artisan test
```

### Test Coverage Highlights:
* `EtaServiceTest`: Tests deterministic delay severity rules (`NONE`, `LOW`, `MEDIUM`, `HIGH`).
* `ShipmentLifecycleTest`: Verifies valid transitions (`CREATED` &rarr; `IN_TRANSIT` &rarr; `DELIVERED`) and proves that terminal states cannot be regressed.
* `ApiShipmentTest`: Verifies Sanctum token authentication, 401 unauthenticated denials, and customer tenancy authorization rules.
* `WebNavigationTest`: Tests view controllers, guest redirects, and role-based access restrictions.

---

## 📡 REST API Documentation

A complete Postman collection is included in the project root:
* **File:** `cargotrace_postman_collection.json`
* **Import:** Open Postman &rarr; Click **Import** &rarr; Select `cargotrace_postman_collection.json`.

### Core API Endpoints:
* `POST /api/login` & `POST /api/register`
* `GET /api/shipments` (with server-side pagination & filters)
* `POST /api/shipments` (Operator/Admin)
* `GET /api/shipments/{id}` (Tenancy-checked)
* `GET /api/shipments/{id}/tracking` & `POST /api/shipments/{id}/tracking`
* `GET /api/carriers` & `POST /api/carriers`
* `GET /api/locations` & `POST /api/locations`
* `POST /api/shipments/{id}/exceptions` & `PATCH /api/exceptions/{id}`

---

## 🌐 Deployment Guidelines

This project can be deployed easily to any modern cloud PaaS (e.g., Railway, Render, Fly.io, or VPS):
1. Create a MySQL database instance in the cloud.
2. Push your repository to GitHub.
3. Set environment variables on the cloud provider (`APP_KEY`, `APP_ENV=production`, `DB_*`).
4. Run `php artisan migrate --force --seed` during the build or post-deploy hook.

---

## 📜 Attribution & Libraries
* **Framework:** [Laravel](https://laravel.com)
* **Styling:** [Tailwind CSS](https://tailwindcss.com)
* **Mapping:** [Leaflet.js](https://leafletjs.com) & [OpenStreetMap](https://www.openstreetmap.org)
* **Charts:** [Chart.js](https://www.chartjs.org)
* **Atmospheric API:** [Open-Meteo](https://open-meteo.com)
* **Icons:** [Phosphor Icons](https://phosphoricons.com)

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ShipmentWebController;
use App\Http\Controllers\Web\TrackingWebController;
use App\Http\Controllers\Web\ExceptionWebController;
use App\Http\Controllers\Web\CarrierWebController;
use App\Http\Controllers\Web\LocationWebController;
use App\Http\Controllers\Web\AuditLogWebController;
use App\Http\Controllers\Web\NotificationWebController;
use App\Http\Controllers\Web\UserWebController;

// Locale Switcher (Indonesian / English)
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'], true)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('locale.switch');

// Public Landing & Quick Tracking
Route::get('/', [ShipmentWebController::class, 'publicHome'])->name('home');

Route::get('/track', [ShipmentWebController::class, 'publicTrack'])->name('public.track');
Route::get('/track/waybill', [ShipmentWebController::class, 'publicWaybill'])->name('public.waybill');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shipments
    Route::get('/shipments', [ShipmentWebController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentWebController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentWebController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{id}', [ShipmentWebController::class, 'show'])->name('shipments.show');
    Route::delete('/shipments/{id}', [ShipmentWebController::class, 'destroy'])->name('shipments.destroy');

    // Tracking Events & Exceptions
    Route::post('/shipments/{id}/tracking', [TrackingWebController::class, 'store'])->name('tracking.store');
    Route::post('/shipments/{id}/exceptions', [ExceptionWebController::class, 'store'])->name('exceptions.store');
    Route::post('/exceptions/{id}/resolve', [ExceptionWebController::class, 'resolve'])->name('exceptions.resolve');

    // Configuration / Master Data
    Route::get('/carriers', [CarrierWebController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierWebController::class, 'store'])->name('carriers.store');

    Route::get('/locations', [LocationWebController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationWebController::class, 'store'])->name('locations.store');

    // Audit Logs & Users (Admin)
    Route::get('/audit-logs', [AuditLogWebController::class, 'index'])->name('audit_logs.index');
    Route::get('/users', [UserWebController::class, 'index'])->name('users.index');
    Route::post('/users', [UserWebController::class, 'store'])->name('users.store');
    Route::patch('/users/{id}/role', [UserWebController::class, 'updateRole'])->name('users.updateRole');

    // Notifications (In-App)
    Route::get('/notifications', [NotificationWebController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationWebController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationWebController::class, 'markAllRead'])->name('notifications.readAll');
});

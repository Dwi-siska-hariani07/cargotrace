<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique()->index();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers');
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destination_id')->constrained('locations');
            $table->enum('transport_type', ['SEA', 'AIR', 'LAND'])->default('SEA');
            $table->string('cargo_type');
            $table->decimal('total_weight_kg', 10, 2);
            $table->decimal('total_volume_cbm', 10, 2)->nullable();
            
            // Scheduling & Timestamps
            $table->dateTime('expected_departure');
            $table->dateTime('expected_arrival');
            $table->dateTime('estimated_arrival');
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            
            // Status & Delay Tracking
            $table->enum('current_status', [
                'CREATED', 
                'BOOKED', 
                'PICKED_UP', 
                'IN_TRANSIT', 
                'OUT_FOR_DELIVERY', 
                'DELIVERED', 
                'CANCELLED'
            ])->default('CREATED')->index();
            
            $table->boolean('is_delayed')->default(false)->index();
            $table->integer('delay_hours')->default(0);
            $table->enum('delay_severity', ['NONE', 'LOW', 'MEDIUM', 'HIGH'])->default('NONE');
            $table->text('delay_reason')->nullable();
            
            // Current Location for Map quick view
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->string('current_location_name')->nullable();
            $table->text('notes')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

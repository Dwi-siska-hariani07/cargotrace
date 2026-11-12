<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('vessel_or_flight', 120)->nullable()->after('cargo_type');
            $table->string('container_or_awb', 100)->nullable()->after('vessel_or_flight');
            $table->string('receiver_name', 150)->nullable()->after('container_or_awb');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['vessel_or_flight', 'container_or_awb', 'receiver_name']);
        });
    }
};

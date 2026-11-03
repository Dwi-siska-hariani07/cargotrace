<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. IDJKT, SGSIN, NLRTM, USLAX
            $table->string('name'); // Port of Tanjung Priok, Port of Singapore
            $table->enum('type', ['SEAPORT', 'AIRPORT', 'WAREHOUSE', 'CUSTOMS_CHECKPOINT'])->default('SEAPORT');
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('start_mileage', 10, 2)->nullable()->after('status');
            $table->decimal('end_mileage', 10, 2)->nullable()->after('start_mileage');
            $table->decimal('total_distance', 10, 2)->nullable()->after('end_mileage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['start_mileage', 'end_mileage', 'total_distance']);
        });
    }
};

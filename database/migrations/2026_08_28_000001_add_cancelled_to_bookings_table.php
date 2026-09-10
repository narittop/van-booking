<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'cancelled' to status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','received','approved','rejected','completed','cancelled') DEFAULT 'pending'");

        Schema::table('bookings', function (Blueprint $table) {
            $table->text('cancelled_reason')->nullable()->after('admin_notes');
            $table->string('cancelled_by_name')->nullable()->after('cancelled_reason');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('cancelled_by_name');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['cancelled_reason', 'cancelled_by_name', 'cancelled_by', 'cancelled_at']);
        });

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','received','approved','rejected','completed') DEFAULT 'pending'");
    }
};

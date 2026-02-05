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
        // Create director_departments pivot table
        Schema::create('director_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('department');
            $table->timestamps();

            $table->unique(['user_id', 'department']);
        });

        // Add received_by and received_at to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('received_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable()->after('received_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['received_by', 'received_at']);
        });

        Schema::dropIfExists('director_departments');
    }
};

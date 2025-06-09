<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Add this line

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear existing data to avoid conflicts
        DB::table('events')->truncate();
        
        Schema::table('events', function (Blueprint $table) {
            $table->datetime('start')->after('description');
            $table->datetime('end')->nullable()->after('start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['start', 'end']);
        });
    }
};
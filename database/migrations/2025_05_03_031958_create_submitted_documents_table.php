<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('submitted_documents', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->id();
            $table->string('subject');
            $table->text('overview')->nullable();
            $table->string('academic_year');
            $table->string('venue');
            $table->timestamp('proposed_date_time');
            $table->integer('hours');
            $table->string('attendees');
            $table->enum('attendees_range', ['10-50', '50-100', '100-250', '250-500', 'Above 500']);
            $table->float('fees');
            $table->string('type');
            $table->string('control_tag')->unique()->default('AUTO');
            $table->enum('status', ['Pending', 'Under Review', 'Approved', 'Returned'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submitted_documents');
    }
};

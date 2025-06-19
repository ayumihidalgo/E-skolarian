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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_webmail')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->id();
            $table->string('subject');
            $table->text('overview');
            $table->string('academic_year');
            $table->string('venue')->nullable();
            $table->timestamp('proposed_date_time')->nullable();
            $table->integer('hours')->nullable();
            $table->string('attendees')->nullable();
            $table->enum('attendees_range', ['10-50', '50-100', '100-250', '250-500', 'Above 500'])->nullable();
            $table->float('fees')->nullable();
            $table->string('type');
            $table->string('control_tag')->unique()->default('AUTO');
            $table->enum('status', ['Pending', 'Under Review', 'Approved', 'Returned', 'Resubmitted'])->default('Pending');
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

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
            $table->text('summary')->nullable();
            $table->enum('type', ['Event Proposal','General Plan of Activities','Calendar of Activities','Accomplishment Report','Constitution and By-Laws','Request Letter','Off Campus','Petition and Concern']);
            $table->string('control_tag')->unique()->default('AUTO');
            $table->enum('status', ['Pending', 'Under Review', 'Approved', 'Rejected', 'Resubmit'])->default('Pending');
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

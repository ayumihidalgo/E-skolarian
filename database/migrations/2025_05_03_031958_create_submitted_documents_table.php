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
            $table->string('received_by');
            $table->id();
            $table->string('subject');
            $table->text('summary')->nullable();
            $table->string('type');
            $table->string('control_tag')->unique()->default('AUTO');
            $table->enum('status', ['Pending', 'Under Review', 'Approved', 'Rejected', 'Resubmit'])->default('Pending');
            $table->string('file_path')->nullable();
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

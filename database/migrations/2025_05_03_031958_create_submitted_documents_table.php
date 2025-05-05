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
            $table->id();
            $table->string('control_tag')->unique()->default('AUTO');
            $table->string('doc_receiver');
            $table->string('subject');
            $table->string('doc_type');
            $table->text('summary')->nullable();
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

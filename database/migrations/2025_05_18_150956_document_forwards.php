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
        Schema::create('document_forwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('forwarded_by');
            $table->unsignedBigInteger('forwarded_to');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('submitted_documents')->onDelete('cascade');
            $table->foreign('forwarded_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('forwarded_to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_forwards');
    }
};
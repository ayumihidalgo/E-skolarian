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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewed_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('document_id')->constrained('submitted_documents')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('status', ['Pending', 'Under Review', 'Approved', 'Rejected', 'Resubmit'])->default('Under Review');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
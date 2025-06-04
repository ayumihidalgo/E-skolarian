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
        Schema::create('document_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('submitted_documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action_type');  // 'review', 'forward', 'resubmit', 'approve', 'reject', etc.
            $table->string('status');       // 'under_review', 'forwarded', 'resubmitted', 'approved', 'rejected', etc.
            $table->text('message')->nullable();
            $table->foreignId('forwarded_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('related_review_id')->nullable()->constrained('reviews')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_timeline');
    }
};
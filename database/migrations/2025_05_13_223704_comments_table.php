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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');  // Retained for document association
            $table->unsignedBigInteger('sent_by');
            $table->unsignedBigInteger('received_by');
            $table->text('comment');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign key constraints
            $table->foreign('sent_by')->references('id')->on('users');
            $table->foreign('received_by')->references('id')->on('users');
            $table->foreign('document_id')->references('id')->on('submitted_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentVersionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('uploaded_by');
            $table->integer('version');
            $table->string('file_path');
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at');

            // Foreign key constraints
            $table->foreign('document_id')
                  ->references('id')->on('submitted_documents')
                  ->onDelete('cascade');

            $table->foreign('uploaded_by')
                  ->references('user_id')->on('submitted_documents') // Can be ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
}
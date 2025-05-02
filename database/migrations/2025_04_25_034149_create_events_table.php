<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
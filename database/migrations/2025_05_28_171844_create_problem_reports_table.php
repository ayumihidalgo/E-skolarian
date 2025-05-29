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
    Schema::create('problem_reports', function (Blueprint $table) {
        $table->id();
        $table->string('email');           // PUP Webmail
        $table->text('description');       // Problem Description
        $table->string('screenshot_path')->nullable();  // Screenshot file path
        $table->timestamps();
    });
}

};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //Setting up the Database Schema for Notifications
    
    public function up(): void
    {
        // Create the notifications table
        // This table will store notifications for users
        // Each notification will have a user_id, title, message, and a read status
        // The user_id will reference the id in the users table
        // The table will also have timestamps for created_at and updated_at
        // The is_read column will default to false, indicating that the notification has not been read yet
        // The foreign key constraint ensures that if a user is deleted, their notifications will also be deleted
       
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        //delete or drop a table if the 'notifications' table exists
        // This is the reverse of the up() method
        // It ensures that the notifications table is removed from the database
        // This is useful for rolling back migrations
        // The dropIfExists method checks if the table exists before attempting to drop it
        // This prevents errors if the table does not exist
        Schema::dropIfExists('notifications');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAudienceAndDeadlineToAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('audience')->default('all')->after('content'); // 'all' or 'custom'
            $table->json('audience_students')->nullable()->after('audience'); // store selected student IDs as JSON
            $table->timestamp('deadline')->nullable()->after('audience_students'); // schedule/deadline for announcement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('audience');
            $table->dropColumn('audience_students');
            $table->dropColumn('deadline');
        });
    }
}

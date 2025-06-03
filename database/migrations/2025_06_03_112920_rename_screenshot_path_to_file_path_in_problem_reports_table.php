<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameScreenshotPathToFilePathInProblemReportsTable extends Migration
{
    public function up()
    {
        Schema::table('problem_reports', function (Blueprint $table) {
            $table->renameColumn('screenshot_path', 'file_path');
        });
    }

    public function down()
    {
        Schema::table('problem_reports', function (Blueprint $table) {
            $table->renameColumn('file_path', 'screenshot_path');
        });
    }
}

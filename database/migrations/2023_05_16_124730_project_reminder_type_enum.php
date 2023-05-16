<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectReminderTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema :: table('project_reminders', function (Blueprint $table) {
            $table->enum('type', ['deadline', 'reminder', 'impact'])->default('deadline')->after('name');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema :: table('project_reminders', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}

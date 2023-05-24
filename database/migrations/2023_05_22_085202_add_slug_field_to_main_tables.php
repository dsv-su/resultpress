<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugFieldToMainTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string("slug")->nullable()->after("id");
        });
        Schema::table('outcomes', function (Blueprint $table) {
            $table->string("slug")->nullable()->after("id");
        });
        Schema::table('outputs', function (Blueprint $table) {
            $table->string("slug")->nullable()->after("id");
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->string("slug")->nullable()->after("id");
        });
        Schema::table('project_reminders', function (Blueprint $table) {
            $table->string("slug")->nullable()->after("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('outcomes', function (Blueprint $table) {
            if (Schema::hasColumn('outcomes', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('outputs', function (Blueprint $table) {
            if (Schema::hasColumn('outputs', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('project_reminders', function (Blueprint $table) {
            if (Schema::hasColumn('project_reminders', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
}

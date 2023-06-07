<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverallBudgetFieldToProjectTableAndUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table
                ->decimal('overall_budget', 20, 2)
                ->default(0)
                ->after('currency');
        });

        Schema::table('project_updates', function (Blueprint $table) {
            $table
                ->decimal('overall_spent', 20, 2)
                ->default(0)
                ->after('summary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('overall_budget');
        });

        Schema::table('project_updates', function (Blueprint $table) {
            $table->dropColumn('overall_spent');
        });
    }
}

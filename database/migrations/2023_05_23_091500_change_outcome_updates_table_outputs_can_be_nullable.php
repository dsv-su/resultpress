<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOutcomeUpdatesTableOutputsCanBeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            "outcome_updates",
            function (Blueprint $table) {
                $table->string("outputs")->nullable()->change();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            "outcome_updates",
            function (Blueprint $table) {
                $table->string("outputs")->nullable(false)->change();
            }
        );
    }
}

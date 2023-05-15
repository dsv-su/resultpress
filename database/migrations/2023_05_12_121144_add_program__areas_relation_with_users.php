<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgramAreasRelationWithUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create pivot table to connect program areas with users.
        Schema::create('area_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove foreign key constraints.
        Schema::table('area_user', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['user_id']);
        });
        // Drop pivot table to connect program areas with users.
        Schema::dropIfExists('area_user');
    }
}

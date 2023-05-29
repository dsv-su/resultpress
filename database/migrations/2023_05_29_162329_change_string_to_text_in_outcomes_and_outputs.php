<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStringToTextInOutcomesAndOutputs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->text('name')->change();
        });
        Schema::table('outputs', function (Blueprint $table) {
            $table->text('indicator')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'outcomes', function ( Blueprint $table ) {
            $table->string( 'name' )->change();
        } );
        Schema::table( 'outputs', function ( Blueprint $table ) {
            $table->string( 'indicator' )->change();
        } );
    }
}

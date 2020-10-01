<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_update_id')->constrained();
            $table->foreignId('activity_id')->constrained();
            $table->integer('status');
            $table->decimal('money');
            $table->mediumText('comment');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*
    public function down()
    {
        Schema::dropIfExists('activity_updates');
    }
    */
}

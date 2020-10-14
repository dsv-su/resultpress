<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->mediumText('description')->nullable();
                $table->integer('status');
                $table->date('start')->nullable();
                $table->date('end')->nullable();
                $table->char('currency')->nullable();
                $table->foreignId('user_id')->references('id')->on('users');
                $table->boolean('activities');
                $table->boolean('outputs')->default(0);
                $table->boolean('aggregated_outputs')->default(0);
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
        Schema::dropIfExists('projects');
    }
*/
}

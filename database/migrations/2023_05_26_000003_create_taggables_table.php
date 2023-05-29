<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaggablesTable extends Migration
{
    public function up()
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('term_id')->references('id')->on('terms')->onDelete('cascade');
            $table->integer('taggable_id')->unsigned();
            $table->string('taggable_type');
        });
    }

    public function down()
    {
        Schema::drop('taggables');
    }
}

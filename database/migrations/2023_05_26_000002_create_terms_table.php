<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsTable extends Migration
{
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->references('id')->on('taxonomies')->onDelete('cascade');
            $table->text('slug');
            $table->text('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('terms');
    }
}

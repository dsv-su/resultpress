<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->text('body')->nullable();
                $table->json('meta')->nullable();
                $table->morphs('commentable');
                $table->timestamps();
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
            'comments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropMorphs('commentable');
            }
        );
        Schema::dropIfExists('comments');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomeUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcome_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_update_id')->constrained();
            $table->foreignId('outcome_id')->constrained();
            $table->date('completed_on')->nullable();
            $table->text('summary')->nullable();
            $table->longText('outputs');
            $table->timestamps();
        });
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropColumn('outputs');
            $table->dropColumn('summary');
            $table->dropColumn('completed_on');
            $table->dropColumn('completed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outcome_updates');
    }
}

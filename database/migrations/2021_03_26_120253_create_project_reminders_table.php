<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->string('name')->nullable();
            $table->boolean('reminder')->default(true);
            $table->integer('reminder_due_days')->default(7);
            $table->date('set')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_reminders');
    }
}

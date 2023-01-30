<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectAddObjectTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'projects', function (Blueprint $table) {
                $table->enum(
                    'object_type', 
                    [
                    'project',
                    'project_history',
                    'project_add_request',
                    'project_change_request',
                    ]
                )->default('project')->after('id');
                $table->unsignedBigInteger('object_id')->nullable()->after('object_type');

                $table->index('object_type');
                $table->foreign('object_id')->references('id')->on('projects');
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
        $table->dropColumn('object_type');
    }
}

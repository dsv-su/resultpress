<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelEnumToTaxonomyTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxonomy_types', function (Blueprint $table) {
            $table->enum('model',
                [
                    Project::class,
                    ProjectUpdate::class,
                    Area::class,
                    Organisation::class,
                ],
            )->default(Project::class)->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxonomy_types', function (Blueprint $table) {
            $table->dropColumn('model');
        });
    }
}

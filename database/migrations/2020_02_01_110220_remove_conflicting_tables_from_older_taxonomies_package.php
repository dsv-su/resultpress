<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveConflictingTablesFromOlderTaxonomiesPackage extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('taxonomies')) {
            if (!Schema::hasColumn('taxonomies', 'type')) {
                Schema::dropIfExists('taxonomies');
            }
        }
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taggables');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down migration
    }
}

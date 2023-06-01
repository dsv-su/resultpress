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
        if (Schema::hasTable('taxonomies')) {
            if (!Schema::hasColumn('taxonomies', 'type')) {
                Schema::disableForeignKeyConstraints();
                Schema::dropIfExists('taxonomies');
                Schema::enableForeignKeyConstraints();
            }
        }
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taggables');

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

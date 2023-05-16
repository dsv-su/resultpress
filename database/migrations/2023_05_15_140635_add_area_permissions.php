<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAreaPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert("INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES ('view-areas', 'web', NOW(), NOW())");
        DB::insert("INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES ('edit-areas', 'web', NOW(), NOW())");
        DB::insert("INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES ('system-admin', 'web', NOW(), NOW())");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::delete("DELETE FROM permissions WHERE name = 'view-areas'");
        DB::delete("DELETE FROM permissions WHERE name = 'edit-areas'");
        DB::delete("DELETE FROM permissions WHERE name = 'system-admin'");
    }
}

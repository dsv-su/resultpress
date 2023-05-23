<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeFieldToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->enum('type', ['text', 'textarea', 'image', 'file', 'select', 'checkbox', 'radio', 'email', 'password', 'number', 'date', 'time', 'datetime', 'color', 'range', 'url', 'tel', 'search', 'hidden', 'month', 'week', 'currency', 'language', 'country', 'timezone', 'html', 'markdown', 'wysiwyg', 'code', 'json', 'key-value'])->default('text')->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}

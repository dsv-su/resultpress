<?php

use Illuminate\Database\Seeder;

class ActivityUpdatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_updates')->insert([
            'project_update_id' => 1,
            'activity_id' => 2,
            'status' => 1,
            'money' => 0,
            'comment' => 'My first literature review',
            'date' => '2020-09-23',
            'created_at' => '2020-09-22 13:40:29',
            'updated_at' => '2020-09-22 13:40:29',
        ]);
        DB::table('activity_updates')->insert([
            'project_update_id' => 1,
            'activity_id' => 2,
            'status' => 1,
            'money' => 0,
            'comment' => 'My first literature review',
            'date' => '2020-09-23',
            'created_at' => '2020-09-22 13:40:35',
            'updated_at' => '2020-09-22 13:40:35',
        ]);
    }
}

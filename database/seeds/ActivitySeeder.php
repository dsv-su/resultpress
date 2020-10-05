<?php

use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activities')->insert([
            'title' => 'Literature review',
            'description' => '',
            'start' => '2020-09-30',
            'end' => '2020-10-30',
            'budget' => 0,
            'project_id' => 1,
            'created_at' => '2020-09-22 13:35:17',
            'updated_at' => '2020-09-24 12:11:37',
        ]);
        DB::table('activities')->insert([
            'title' => 'Methodology',
            'description' => '',
            'start' => '2020-09-30',
            'end' => '2020-11-30',
            'budget' => 0,
            'project_id' => 1,
            'created_at' => '2020-09-22 13:38:51',
            'updated_at' => '2020-09-24 12:11:37',
        ]);
    }
}

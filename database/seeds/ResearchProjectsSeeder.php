<?php

use Illuminate\Database\Seeder;

class ResearchProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->insert([
            'name' => 'Research project',
            'description' => 'A generic research project',
            'status' => 1,
            'currency' => 'SEK',
            'activities' => 2,
            'outputs' => 0,
            'aggregated_outputs' => 0,
            'start' => '2020-09-30',
            'end' => '2021-04-30',
            'user_id' => 1,
            'created_at' => '2020-09-22 13:35:17',
            'updated_at' => '2020-09-24 12:11:37',
        ]);
    }
}

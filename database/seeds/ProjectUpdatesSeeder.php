<?php

use Illuminate\Database\Seeder;

class ProjectUpdatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_updates')->insert([
            'project_id' => 1,
            'summary' => '',
            'status' => 0,
            'internal_comment' => 'Test Internal comment',
            'partner_comment' => 'Test Partner comment',
            'nominated' => 0,
            'created_at' => '2020-09-22 13:40:29',
            'updated_at' => '2020-09-22 13:40:29',
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class DefaultProjectAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_areas')->insert([
            'project_id' => 1,
            'area_id' => 1,
            'created_at' => '2020-11-16 13:35:17',
            'updated_at' => '2020-11-16 13:36:37',
        ]);
    }
}

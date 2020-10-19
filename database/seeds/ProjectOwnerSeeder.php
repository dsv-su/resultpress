<?php

use Illuminate\Database\Seeder;

class ProjectOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_owners')->insert([
            'project_id' => 1,
            'user_id' => 1,
            'created_at' => '2020-09-22 13:35:17',
            'updated_at' => '2020-09-24 12:11:37',
        ]);
    }
}

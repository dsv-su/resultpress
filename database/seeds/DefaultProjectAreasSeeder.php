<?php

use Illuminate\Database\Seeder;

class DefaultProjectAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('areas')->insert([
            'name' => 'Program Area 1',
            'description' => 'Description of Program area 1',
            'created_at' => '2020-11-16 13:35:17',
            'updated_at' => '2020-11-16 13:36:37',
        ]);
        DB::table('areas')->insert([
            'name' => 'Program Area 2',
            'description' => 'Description of Program area 2',
            'created_at' => '2020-11-16 13:35:17',
            'updated_at' => '2020-11-16 13:36:37',
        ]);
        DB::table('areas')->insert([
            'name' => 'Program Area 3',
            'description' => 'Description of Program area 3',
            'created_at' => '2020-11-16 13:35:17',
            'updated_at' => '2020-11-16 13:36:37',
        ]);
        DB::table('areas')->insert([
            'name' => 'Program Area 4',
            'description' => 'Description of Program area 4',
            'created_at' => '2020-11-16 13:35:17',
            'updated_at' => '2020-11-16 13:36:37',
        ]);
    }
}

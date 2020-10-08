<?php

use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Undefined',
            'email' => 'spideradmin@dsv.su.se',
            'password' => bcrypt('1234'),
            'created_at' => '2020-09-22 13:35:17',
            'updated_at' => '2020-09-24 12:11:37',
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(DefaultUserSeeder::class);
        $this->call(ResearchProjectsSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(ProjectUpdatesSeeder::class);
        $this->call(ActivityUpdatesSeeder::class);
    }
}

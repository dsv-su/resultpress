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
    //    $this->call(ResearchProjectsSeeder::class); //Default project -> remove in production
     //   $this->call(ProjectOwnerSeeder::class); //Default project -> remove in production
     //   $this->call(ActivitySeeder::class); //Default project -> remove in production
     //   $this->call(ProjectUpdatesSeeder::class); //Default project -> remove in production
     //   $this->call(ActivityUpdatesSeeder::class); //Default project -> remove in production
        $this->call(PermissionTableSeeder::class);
    //    $this->call(PermissionResearchProjectTableSeeder::class); //Default project -> remove in production
        $this->call(CreateRolesSeeder::class);
    //    $this->call(DefaultProjectAreasSeeder::class);
     //   $this->call(DefaultProjectAreaSeeder::class);

    }
}

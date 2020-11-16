<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);
        $user->assignRole('Administrator');
        $user->assignRole('Spider');
        $user->assignRole('Partner');
    }
    /*
     * To seed this seeder: php artisan db:seed --class=CreateAdminUserSeeder
     */
}

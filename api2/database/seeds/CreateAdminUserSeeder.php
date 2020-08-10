<?php

use Illuminate\Database\Seeder;

use App\User;

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

        $user = User::create([

        	'name' => 'Admin', 

        	'email' => 'admin@test.com',

        	'password' => bcrypt('123456')

        ]);

  

        $role1 = Role::create(['name' => 'Admin']);
        $role2 = Role::create(['name' => 'Teacher']);
        $role3 = Role::create(['name' => 'Student']);

   

        $permissions = Permission::pluck('id','id')->all();

  

        $role1->syncPermissions($permissions);
        $role2->syncPermissions($permissions);
        $role3->syncPermissions($permissions);

   

        $user->assignRole([$role1->id]);

    }

}
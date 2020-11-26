<?php

namespace Totaa\TotaaPermission\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Role_Permission_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Permission::where("name", "view-role")->count() == 0) {
            $permission = Permission::create(['name' => 'view-role', "description" => "Xem Role", "group" => "Role", "order" => 1, "lock" => true,]);
        } else {
            $permission = Permission::where("name", "view-role")->first();
        }

        if (Role::where("name", "super-admin")->count() == 0) {
            $roles[] =  Role::create(['name' => 'super-admin', "description" => "Super Admin", "group" => "Admin", "order" => 1, "lock" => true,]);
        } else {
            $roles[]= Role::where("name", "super-admin")->first();
        }

        if (Role::where("name", "admin")->count() == 0) {
            $roles[] = Role::create(['name' => 'admin', "description" => "Admin", "group" => "Admin", "order" => 1, "lock" => true,]);
        } else {
            $roles[]= Role::where("name", "admin")->first();
        }

        $permission->syncRoles($roles);
    }
}

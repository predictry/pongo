<?php

use App\Models\PermissionRole,
    App\Models\Role;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:50:36 PM
 * File         : RoleSeeder.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class PermissionRoleSeeder extends Seeder
{

    public function run()
    {

        $admin_role           = Role::where('name', 'Administrator')->first();
        $admin_permission_ids = [
            '1'
        ];

        foreach ($admin_permission_ids as $id) {
            PermissionRole::create([
                'permission_id' => $id,
                'role_id'       => $admin_role->id
            ]);
        }
    }

}

/* End of file RoleSeeder.php */
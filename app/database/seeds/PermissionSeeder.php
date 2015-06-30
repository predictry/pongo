<?php

use App\Models\Permission;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:50:36 PM
 * File         : RoleSeeder.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class PermissionSeeder extends Seeder
{

    public function run()
    {
        $permissions = [
            'view_admin_dashboard_panel',
            'edit_owned_profile',
            'edit_owned_password'
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name'         => $permission,
                'display_name' => ucwords(str_replace('_', ' ', $permission))
            ]);
        }
    }

}

/* End of file RoleSeeder.php */
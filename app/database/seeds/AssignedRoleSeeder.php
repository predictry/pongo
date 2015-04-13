<?php

use App\Models\Account,
    App\Models\AssignedRole,
    App\Models\Role;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:50:36 PM
 * File         : RoleSeeder.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class AssignedRoleSeeder extends Seeder
{

    public function run()
    {

        $admin_account = Account::find(1);
        $admin_role    = Role::where('name', 'Administrator')->first();

        AssignedRole::create([
            'user_id' => $admin_account->id,
            'role_id' => $admin_role->id
        ]);
    }

}

/* End of file RoleSeeder.php */
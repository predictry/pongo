<?php

use App\Models\Role;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:50:36 PM
 * File         : RoleSeeder.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class RoleSeeder extends Seeder
{

    public function run()
    {
        $roles = [
            'Root',
            'Administrator',
            'User',
            'Member'
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }
    }

}

/* End of file RoleSeeder.php */
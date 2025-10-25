<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $guard_name = "web";
        $now = now();
        $common = [
            "guard_name" => $guard_name,
            "created_at" => $now,
            "updated_at" => $now,
        ];
        $permissions = [
            //User Management
            [
                "name" => "none",
                "description" => "No Permission",
                ...$common,
            ],
            [
                "name" => "users_add",
                "description" => "Add new user",
                ...$common,
            ],
            [
                "name" => "users_list",
                "description" => "Show users table",
                ...$common,
            ],
            [
                "name" => "users_edit-profile",
                "description" => "Update users profile",
                ...$common,
            ],
            [
                "name" => "users_edit-account",
                "description" => "Update users account credentials",
                ...$common,
            ],
            [
                "name" => "users_edit-permission",
                "description" => "Update users Roles and/or permissions",
                ...$common,
            ],
            [
                "name" => "users_change-status",
                "description" => 'Activate or deactivate users\' account',
                ...$common,
            ],

            // User Roles
            [
                "name" => "roles_list",
                "description" => "Show Roles table",
                ...$common,
            ],
            [
                "name" => "roles_add",
                "description" => "Add new Role",
                ...$common,
            ],
            [
                "name" => "roles_edit",
                "description" => "Edit existing roles",
                ...$common,
            ],
            [
                "name" => "roles_delete",
                "description" => "Delete roles (Warning! this action is permanent and non recoverable.)",
                ...$common,
            ],

            // Account management
            [
                "name" => "self_update-profile",
                "description" => "Allow users to change their profile information",
                ...$common,
            ],
            [
                "name" => "self_update-account",
                "description" => "Allow users to change their account information",
                ...$common,
            ],
            [
                "name" => "self_change-password",
                "description" => "Allow users to change their account password",
                ...$common,
            ],
            [
                "name" => "self_change-avatar",
                "description" => "Allow users to change their profile image",
                ...$common,
            ],
        ];

        Permission::insert($permissions);
    }
}

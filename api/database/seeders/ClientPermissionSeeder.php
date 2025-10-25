<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class ClientPermissionSeeder extends Seeder {
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $guard_name = "client";
        $now = now();
        $common = [
            "guard_name" => $guard_name,
            "created_at" => $now,
            "updated_at" => $now,
        ];

        $permissions = [];

        Permission::insert($permissions);
    }
}

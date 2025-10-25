<?php

namespace App\Observers;

use App\models\User;

use Database\Seeders\RoleSeeder;

class TeamObserver {
    public function created($team) {
        $session_team_id = getPermissionsTeamId();
        setPermissionsTeamId($team->id);

        $this->CreateRoles($team);
        $this->attachSuperman();

        setPermissionsTeamId($session_team_id);
    }

    private function CreateRoles($team) {
        $seeder = new RoleSeeder();
        $roles = $seeder->getRoles(true);
        $roles->each(function ($role) use ($team, $seeder) {
            $insert = $seeder->formatInsert($role, true, $team->id);
            $seeder->createRole($insert);
        });
    }

    private function attachSuperman() {
        $admins = User::whereRelation("roles_all", "name", config("mitd.superman"))->get();
        foreach ($admins as $admin) {
            $admin->assignRole(config("mitd.superman"));
        }
    }
}

<?php

namespace App\Observers;

use App\Models\User;

class UserTeamObserver {
    /**
     * Handle the User "created" event.
     */
    public function creating(User $user): void {
        if (!$user[config("permission.column_names.team_foreign_key")]) {
            $user[config("permission.column_names.team_foreign_key")] = getPermissionsTeamId();
        }
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy {
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool {
        return $user->canAny([
            "users_add",
            "users_list",
            "users_edit-profile",
            "users_edit-account",
            "users_edit-permission",
            "users_give-direct-permissions",
            "users_change-status",
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, User $model): bool {
    // }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool {
        return $user->can("users_add");
    }

    public function updateAccount(User $user, User $model): bool {
        return $user->can("users_edit-account") && $user->id !== $model->id;
    }

    public function updateProfile(User $user, User $model): bool {
        return $user->can("users_edit-profile") && $user->id !== $model->id;
    }

    public function updatePermission(User $user, User $model): bool {
        return $user->can("users_edit-permission") && $user->id !== $model->id;
    }

    public function toggleUsers(User $user, User $model){
        return $user->can("users_change-status") && $user->id !== $model->id;
    }
    
    public function updateOwnAccount(User $user, User $model): bool {
        return $user->can("self_update-account") && $user->id === $model->id;
    }

    public function updateOwnPassword(User $user, User $model): bool {
        return $user->can("self_change-password") && $user->id === $model->id;
    }

    public function updateOwnProfile(User $user, User $model): bool {
        return $user->can("self_update-profile") && $user->id === $model->id;
    }

    public function updateOwnAvatar(User $user, User $model): bool {
        return $user->can("self_change-avatar") && $user->id === $model->id;
    }
    
    /**
     * Determine whether the user can update the model.
     */
    // public function update(User $user, User $model): bool {
    // }

    /**
     * Determine whether the user can delete the model.
     */
    // public function delete(User $user, User $model): bool {
    // }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, User $model): bool {
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, User $model): bool {
    // }
}

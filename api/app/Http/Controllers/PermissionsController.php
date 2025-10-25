<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Permission\ListPermissionRequest;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;

use App\Traits\PermissionsTrait;

class PermissionsController extends Controller {
    use PermissionsTrait;

    public function list(ListPermissionRequest $request) {
        $guards = array_keys(config("auth.guards"));
        $guard = $request->input("g");
        $guard = in_array($guard, $guards) ? $guard : null;

        return $this->getPermissions($request, $guard);
    }

    public function store(StorePermissionRequest $request) {
        $validated = $request->validated();
        $guard = $request->input("g");

        $guard = in_array($guard, array_keys(config("auth.guards"))) ? $guard : Auth::getDefaultDriver();
        $permission = Permission::create([
            "name" => $validated["name"],
            "description" => $validated["description"] ?? null,
            "guard_name" => $guard,
        ]);

        return [
            "data" => new PermissionResource($permission),
            "message" => "Permission added successfully",
        ];
    }

    public function update(UpdatePermissionRequest $request, Permission $permission) {
        $validated = $request->validated();
        $permission->update([
            "name" => $validated["name"],
            "description" => $validated["description"],
        ]);

        return [
            "data" => new PermissionResource($permission),
            "message" => "Permission updated successfully",
        ];
    }

    public function destroy(ListPermissionRequest $request, Permission $permission) {
        $permission->users()->detach();
        $permission->delete();

        return [
            "message" => "Permission \"$permission->name\" deleted successfully",
        ];
    }
}

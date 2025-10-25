<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Http\Requests\Role\ListRoleRequest;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Role\DestroyRoleRequest;

use App\Models\Role;
use App\Models\Permission;

use App\Http\Resources\RoleResource;

use App\Traits\PermissionsTrait;

class RolesController extends Controller implements HasMiddleware {
    use PermissionsTrait;

    public static function middleware(): array {
        return [new Middleware("permission:roles_add|roles_edit", only: ["getPermissions"])];
    }

    public function list(ListRoleRequest $request) {
        $search = $request->input("search");
        $limit = max(1, $request->input("limit", 25));
        $page = max(1, $request->input("page", 1));

        $roles = Role::where("name", "!=", config("mitd.superman"))
            ->when(config("permission.teams"), function ($q) {
                $q->whereTeam(getPermissionsTeamId());
            })
            ->where("name", "ilike", "%" . $search . "%")
            ->orderBy("protected", "desc")
            ->orderBy("level")
            ->paginates($limit, $page);

        $roles["data"] = RoleResource::collection($roles["data"]);

        return $roles;
    }

    public function store(CreateRoleRequest $request) {
        $validated = $request->validated();

        $role = Role::create([
            "name" => $validated["name"],
            "description" => $validated["description"],
            "color" => $validated["color"],
        ]);

        $role->syncPermissions($validated["permissions"]);

        return new RoleResource($role);
    }

    public function update(UpdateRoleRequest $request, Role $role) {
        $validated = $request->validated();
        $toUpdate = [
            "description" => $validated["description"],
            "color" => $validated["color"],
        ];
        if (!$role->protected) {
            $toUpdate["name"] = $validated["name"];
        }
        $role->update($toUpdate);
        $role->syncPermissions($validated["permissions"]);

        return new RoleResource($role);
    }

    public function destroy(DestroyRoleRequest $request, Role $role) {
        $role->delete();
        return response([
            "message" => "Role deleted.",
        ]);
    }
}

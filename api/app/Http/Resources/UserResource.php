<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $userID = Auth::id();
        $currentID = $this->id;
        $sameUser = $currentID == $userID;

        $roles = $this->roles
            ->map(function ($role) use ($sameUser) {
                $res = [
                    "name" => $role->name,
                    "color" => $role->color,
                ];
                if (!$sameUser) {
                    $res = array_merge($res, ["id" => $role->hash]);
                }
                return $res;
            })
            ->sortBy("name");

        $result = [
            "id" => $this->when(!$sameUser, $this->hash),
            "email" => $this->email,
            "username" => $this->username,
            "active" => !$this->disabled_at,
            "verified" => $this->email_verified_at,
            "profile" => new ProfileResource($this->profile),

            "roles" => $roles->toArray(),
            "permissions" => $this->when($sameUser, $this->getAllPermissions()->pluck("name"), PermissionResource::collection($this->getDirectPermissions())), // For own account (Profile)
        ];

        if (config("permission.teams") && $sameUser) {
            $team = app(config("mitd.permission.teams_provider"))::find(getPermissionsTeamId());
            $result = array_merge($result, ["team" => app(config("mitd.permission.teams_resource"), ["resource" => $team])]);
        }

        return $result;
    }
}

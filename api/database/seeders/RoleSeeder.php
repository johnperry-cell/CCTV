<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Collection;

use App\Traits\Seeders\HasTeamTrait;
use App\Traits\HasProgressTrait;

class RoleSeeder extends Seeder {
    use WithoutModelEvents, HasTeamTrait, HasProgressTrait;
    /**
     * Define Roles here.
     *
     * @return Collection Returns a collection of roles
     */
    public static function getRoles($excludeAdmin = false): Collection {
        $roles = collect([
            [
                "name" => "Moderator",
                "protected" => 1,
                "color" => "#1ABC9C",
                "level" => 1,
                "description" => "A person with access to some admin features.",
                "permissions" => [
                    "users_add",
                    "users_list",
                    "users_edit-profile",
                    "users_edit-account",
                    "users_edit-permission",
                    "users_change-status",

                    "roles_list",
                    "roles_add",
                    "roles_edit",
                    "roles_delete",
                ],
            ],
            [
                "name" => "User",
                "protected" => 1,
                "color" => "#D3D3D3",
                "level" => 2,
                "description" => "A person with limited access to system features.",
                "permissions" => ["self_change-password", "self_change-avatar", "self_update-profile", "self_update-account"],
            ],
        ]);

        return $roles->when(!$excludeAdmin, function ($collection) {
            return $collection->merge([self::adminRole()]);
        });
    }

    public static function adminRole(): array {
        return [
            "name" => self::superman(),
            "protected" => 1,
            "color" => "#FFD700",
            "level" => 0,
            "description" => 'A person with "Full/Unrestricted" access to admin features.',
            "permissions" => [],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $insert = $this->buildInserts($this->getRoles(), $this->teamsEnabled());
        $progress = $this->getProgressBar("Seeding Roles", count($insert));

        foreach ($insert as $role) {
            $progress->setMessage("Adding <fg=white>\"{$role["name"]}\"</fg=white> Role");
            $this->createRole($role);
            $progress->advance();
        }
        // Role::insert($insert);
        // $this->syncRolePermissions($this->getRoles(true));

        $progress->setMessage("Roles added successfully.");
        $progress->finish();
    }

    public function createRole($role, ?array $permissions = null) {
        $r = Role::create($role);
        $permissions ??= $this->getRoles(true)->where("name", $role["name"])->first();
        if ($permissions && count($permissions["permissions"]) > 0) {
            $r->syncPermissions($permissions["permissions"]);
        }
    }

    public function buildInserts(Collection $roles, bool $withTeams = false, $teams = null): array {
        $now = now();
        $admin = $this->adminRole();
        $teams ??= $this->getTeams($withTeams);

        $insert = collect([$this->formatInsert($admin, $withTeams, null, $now)]);

        $roles->where("name", "!=", $this->superman())->each(function ($role) use (&$insert, $teams, $now, $withTeams) {
            if ($withTeams) {
                $teams->each(function ($team) use (&$insert, $role, $withTeams, $now) {
                    $insert->push($this->formatInsert($role, $withTeams, $team["id"], $now));
                });
            } else {
                $insert->push($this->formatInsert($role, $withTeams, null, $now));
            }
        });

        return $insert->toArray();
    }

    public function syncRolePermissions(Collection $roles) {
        $roles->each($this->syncPermissions(...));
    }

    private function syncPermissions(array $role) {
        $rs = Role::where("name", $role["name"])->get();
        foreach ($rs as $r) {
            $r->syncPermissions($role["permissions"]);
        }
    }

    public function formatInsert($role, bool $teams, ?int $team_id = null, $now = null) {
        return collect([
            "name" => $role["name"],
            "protected" => $role["protected"],
            "description" => $role["description"],
            "color" => $role["color"],
            "level" => $role["level"],
            "guard_name" => "web",
            "created_at" => $now ?? now(),
            "updated_at" => $now ?? now(),
        ])
            ->when($teams, function ($r) use ($team_id) {
                return $r->merge([$this->getTeamsCol() => $team_id]);
            })
            ->toArray();
    }
}

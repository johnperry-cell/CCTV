<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Collection;

use App\Traits\Seeders\HasTeamTrait;
use App\Traits\HasProgressTrait;

class UserSeeder extends Seeder {
    use WithoutModelEvents, HasTeamTrait, HasProgressTrait;

    public function getUsers(): Collection {
        return collect([
            [
                "username" => "whykhamist",
                "email" => null,
                "password" => "wasd1234",
                "role" => $this->superman(),
                $this->getTeamsCol() => 1,
            ],
        ]);
    }

    public function testUsers(): Collection {
        // return collect([]);
        return $this->generateTestUsers(2);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $users = $this->buildInserts()->toArray();
        $progress = $this->getProgressBar("Seeding Users", count($users));
        $progress->setMessage("Creating users...");
        foreach ($users as $user) {
            $user["insert"]["password"] = Hash::make($user["insert"]["password"]);
            $new = User::create($user["insert"]);
            $this->setRoles($new, $user["role"], $this->teamsEnabled());
            $progress->advance();
        }
        $progress->setMessage("Users Created successfully");
        $progress->finish();
    }

    public function setRoles($user, $role, $withTeams) {
        $teams = $this->getTeams($role == $this->superman() && $withTeams, [$user->team]);

        foreach ($teams as $team) {
            if (isset($team["id"])) {
                setPermissionsTeamId($team["id"]);
            }
            $user->assignRole($role);
        }
    }

    public function buildInserts(): Collection {
        return $this->getUsers()
            ->when(config("app.env") != "production", function ($u) {
                return $u->merge($this->testUsers());
            })
            ->map(function ($user) {
                return $this->formatUserForInsert($user);
            });
    }

    public function generateTestUsers($total = 50): Collection {
        return collect()
            ->range(1, $total)
            ->map(function ($i) {
                return $this->setTeam(
                    [
                        "username" => "testuser$i",
                        "email" => "testuser$i@example.com",
                        "password" => "wasd1234",
                        "role" => RoleSeeder::getRoles(true)->random()["name"],
                    ],
                    null
                );
            });
    }

    public function setTeam($user, $team = null): Collection {
        return collect($user)->when(
            $this->teamsEnabled(),
            fn($u) => $u->merge([
                $this->getTeamsCol() => $team ?? $this->getTeams(true)->random()->id,
            ])
        );
    }

    private function formatUserForInsert($user) {
        return collect([
            "insert" => $this->setTeam(
                collect([
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "email_verified_at" => now(),
                    "password" => $user["password"],
                ]),
                $user[$this->getTeamsCol()] ?? null
            )->toArray(),
            "role" => $user["role"],
        ]);
    }
}

<?php

namespace App\Traits\Seeders;

use Illuminate\Support\Collection;

trait HasTeamTrait {
    public static function teamsEnabled(): bool {
        return config("permission.teams");
    }

    public static function superman(): string {
        return config("mitd.superman");
    }

    public static function getTeamsCol(): string {
        return config("permission.column_names.team_foreign_key");
    }

    /**
     * Get teams from database
     *
     * @param bool $teams
     *
     * @return Collection
     */
    public static function getTeams(bool $teams = false, array $default = []): Collection {
        return $teams ? app(config("mitd.permission.teams_provider"))::select("id", "name")->get() : collect($default);
    }
}

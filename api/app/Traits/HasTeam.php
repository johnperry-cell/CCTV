<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasTeam {
    #[Scope]
    public function whereTeam(Builder $query, $team) {
        if (config("permission.teams")) {
            $column = config("permission.column_names.team_foreign_key");

            if (gettype($team) == "integer") {
                return $query->where($column, $team);
            }

            if (gettype($team) == "string") {
                return $query->where($column, app(config("mitd.permission.teams_provider"))->hashToId($team));
            }

            if (get_class($team) == config("mitd.permission.teams_provider")) {
                return $query->where($column, $team->id);
            }

            abort(500, "Invalid team provided");
        }

        return $query;
    }

    #[Scope]
    public function orWhereTeam(Builder $query, $team) {
        if (config("permission.teams")) {
            $column = config("permission.column_names.team_foreign_key");

            if (gettype($team) == "integer") {
                return $query->orWhere($column, $team);
            }

            if (gettype($team) == "string") {
                return $query->orWhere($column, app(config("mitd.permission.teams_provider"))->hashToId($team));
            }

            if (get_class($team) == config("mitd.permission.teams_provider")) {
                return $query->orWhere($column, $team->id);
            }

            abort(500, "Invalid team provided");
        }

        return $query;
    }

    /**
     * Make the user a super admin.
     */
    public function powerUp() {
        if (config("permission.teams")) {
            $teams = app(config("mitd.permission.teams_provider"))::all();
            $session_team = getPermissionsTeamId();
            $teams->each(function ($team) {
                setPermissionsTeamId($team->id);
                $this->assignRole(config("mitd.superman"));
            });
            setPermissionsTeamId($session_team);
        } else {
            $this->assignRole(config("mitd.superman"));
        }
    }

    /**
     * Make the user a normal user.
     */
    public function powerDown() {
        if (config("permission.teams")) {
            $teams = app(config("mitd.permission.teams_provider"))::all();
            $session_team = getPermissionsTeamId();
            $teams->each(function ($team) {
                setPermissionsTeamId($team->id);
                $this->removeRole(config("mitd.superman"));
            });
            setPermissionsTeamId($session_team);
        } else {
            $this->removeRole(config("mitd.superman"));
        }
    }
}

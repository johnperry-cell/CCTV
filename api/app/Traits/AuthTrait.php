<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\Auth\LoginRequest;

use App\Models\User;
use Illuminate\Http\Request;

trait AuthTrait {
    public function getTeams(Request $request) {
        if (!config("permission.teams")) {
            return response(["data" => []]);
        }

        $search = $request->input("search");

        $teams = app(config("mitd.permission.teams_provider"))
            ::select("id", "name")
            ->where("name", "ilike", "%$search%")
            ->get();
        return response([
            "data" => app(config("mitd.permission.teams_resource"), ["resource" => []])->collection($teams),
        ]);
    }

    protected function AttemptLogin(LoginRequest $request, User $user, string $field, bool $fromFrontend = true) {
        $message = null;
        $disabled = false;

        $attempt = $fromFrontend ? $this->cookieLogin($request, $field) : $this->tokenLogin($request, $field, $user);

        if (!$attempt) {
            $disabled = $this->UserLoginFailed($user);
            $message = config($disabled ? "mitd.auth.error.locked" : "mitd.auth.error.invalid");
        }

        return [
            "message" => $message,
            "disabled" => $disabled,
            "success" => $attempt,
        ];
    }

    protected function tokenLogin(LoginRequest $request, string $field, User $user): bool {
        return Hash::check($request->input("password"), $user->password);
    }

    protected function cookieLogin(LoginRequest $request, string $field): bool {
        return Auth::attempt(
            [
                $field => strtolower($request->input("email")),
                "password" => $request->input("password"),
            ],
            $request->input("remember", false)
        );
    }

    protected function UserLoginFailed(User $user) {
        if (!!$user && $user->disabled_at == null) {
            $user->fails++;
            $max = config("mitd.auth.max_attempts");
            if (!is_null($max) && $max > 0 && $user->fails >= (int) $max) {
                $user->disabled_at = now();
            }
            $user->save();
        }
        return $user->disabled_at !== null;
    }
}

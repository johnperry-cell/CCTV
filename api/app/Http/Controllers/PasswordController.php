<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Auth\Events\PasswordReset;

use App\Models\User;

class PasswordController extends Controller {
    public function forgot_password(Request $request) {
        $validate = $request->validate([
            "email" => "required|email",
        ]);

        $status = Password::sendResetLink($request->only("email"));

        switch ($status) {
            case Password::INVALID_USER:
            case Password::RESET_LINK_SENT:
            default:
                return response([
                    "message" => config("mitd.password.message.forgot_password"),
                ]);
                break;
        }
    }

    public function reset_password(Request $request) {
        $request->validate([
            "token" => "required",
            "email" => "required|email",
            "password" => ["required", PasswordRule::defaults(), "confirmed"],
        ]);

        $status = Password::reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function (User $user, string $password) {
                $user
                    ->forceFill(["password" => Hash::make($password)])
                    ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response(["message" => config("mitd.password.message.password_reset")])
            : response(["message" => config("mitd.password.message.password_reset_fail")], 422);
    }
}

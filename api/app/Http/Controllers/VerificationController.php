<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Http\Requests\EmailVerificationRequest;

use App\Http\Resources\UserResource;

use App\MITD\URLBuilder;

class VerificationController extends Controller {
    public function resend() {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            return response(
                [
                    "message" => "Email already verified",
                    "data" => new UserResource($user),
                ],
                422
            );
        }

        $user->sendEmailVerificationNotification();

        return response(
            [
                "message" => "Verification email sent",
                "data" => new UserResource($user),
            ],
            200
        );
    }

    public function verify(EmailVerificationRequest $request, string $id, string $hash) {
        if (!$this->isVerificationLinkValid($request, $id, $hash) || $request->user()->hasVerifiedEmail()) {
            return response(
                [
                    "message" => config("mitd.email.verification.messages.invalid_link"),
                ],
                422
            );
        }
        $request->fulfill();
        return response([
            "message" => "Email verified",
            "data" => new UserResource($request->user()),
        ]);
    }

    private function isVerificationLinkValid(EmailVerificationRequest $request, string $id, string $hash): bool {
        $builder = new URLBuilder(config("mitd.email.domain_url"));
        $builder->paths(config("mitd.email.verification.path"), $id, $hash);
        $builder->query("expires", $request->expires);
        $builder->query("signature", $request->signature);
        return $builder->hasValidSignature();
    }
}

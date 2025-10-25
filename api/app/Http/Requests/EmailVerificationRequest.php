<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

class EmailVerificationRequest extends BaseEmailVerificationRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $id = (string) $this->route("id");
        if (!hash_equals((string) $this->user()->hash, $id)) {
            return false;
        }

        if (!hash_equals(sha1($id . "_" . $this->user()->getEmailForVerification() . "_" . $this->expires), (string) $this->route("hash"))) {
            return false;
        }

        return true;
    }
}

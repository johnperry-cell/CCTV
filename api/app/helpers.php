<?php

use App\MITD\Logger;
use App\Models\User;

if (!function_exists("trail")) {
    function trail(?string $message = null, ?User $user = null) {
        return new Logger($message, $user);
    }
}

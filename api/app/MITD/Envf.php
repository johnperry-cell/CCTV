<?php

namespace App\MITD;

class Envf {
    public static function get(string $env, $default = null) {
        $envVal = env($env, $default);
        return empty($envVal) ? $default : $envVal;
    }
}

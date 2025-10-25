<?php

use App\MITD\Envf;

return [
    /**
     * The name to use for the super admin. Defaults to "Admin".
     */
    "superman" => env("MITD_SUPERMAN", "Admin"),

    /**
     * Authentication Settings
     */
    "auth" => [
        /**
         * The maximum number of failed login attempts before the user is locked out.
         * Defaults to null (no limit).
         */
        "max_attempts" => (int) Envf::get("MITD_AUTH_MAX_ATTEMPTS"),

        /**
         * Error messages to display on failed login attempts.
         */
        "error" => [
            /**
             * The error message to display on a failed login attempt.
             */
            "invalid" => "Username or password is incorrect.",

            /**
             * The error message to display when the the max_attemps limit has been reached.
             */
            "locked" => "Account locked!, please contact your system administrator.",

            /**
             * The error message to display when the user tries to login with an account that has been disabled/locked.
             */
            "disabled" => "Whoops! It appears that your account has been disabled. If you think this is a mistake, please contact us for assistance.",
        ],
    ],

    "permission" => [
        /**
         * The model to use for teams
         * ex. "teams_provider" => \App\Models\Team::class
         */
        "teams_provider" => null,

        /**
         * The resource to use for teams, must containt the "id" and "name" attributes
         *
         */
        "teams_resource" => \App\Http\Resources\TeamsResource::class,
    ],

    "sanctum" => [
        /**
         * The expiration time of the access token
         */
        "ac_expiration" => (int) Envf::get("MITD_SANCTUM_AC_EXPIRATION", 60), // 1 hour

        /**
         * The expiration time of the refresh token
         */
        "rt_expiration" => (int) Envf::get("MITD_SANCTUM_RT_EXPIRATION", 7 * 24 * 60), // 7 days
    ],

    "password" => [
        /**
         * The default password validation rules
         * @see https://laravel.com/docs/11.x/validation#validating-passwords
         */
        "rules" => [
            /**
             * The minimum size of the password
             */
            "min" => 8,

            /**
             * The maximum size of the password
             * Defaults to null (no limit)
             */
            "max" => null,

            /**
             * Makes the password require at least one number
             */
            "numbers" => true,

            /**
             * Makes the password require at least one symbol
             */
            "symbols" => false,

            /**
             * Makes the password require at least one letter
             */
            "letters" => true,

            /**
             * Makes the password require at least one uppercase and one lowercase letter
             * ignored if 'letters' is false
             */
            "mixedCase" => false,

            /**
             * Makes the password require that the password is not in a list of leaked passwords
             */
            "uncompromised" => false,

            /**
             * The threshold at which the password is considered compromised
             */
            "compromisedThreshold" => 0,
        ],

        "message" => [
            /**
             * The message to display when a user sends a password reset request
             */
            "forgot_password" => "A password reset email has been sent IF such an account exists",

            /**
             * The message to display when a user resets their password successfully
             */
            "password_reset" => "Your password has been reset.",

            /**
             * The message to display when a user fails to reset their password
             */
            "password_reset_fail" => "Whoops! Something went wrong. Please try again.",
        ],
    ],

    "email" => [
        "domain_url" => env("APP_ENV") === "production" ? env("APP_URL", "http://localhost") : env("MITD_EMAIL_URL", null),

        "password_reset" => [
            /**
             * The password reset route to append the token to the end of the URL.
             * Please use the client route for this (SPA thing). Make sure the client route accepts a token param.
             * The user's email also be added as a query param to the URL.
             *
             * sample output1: http://127.0.0.1:8080/password/reset/{token}?email=test@email.com
             * sample output2: https://projectname.baguio.gov.ph/password/reset/{token}?email=test@email.com
             */
            "path" => "/password/reset",
        ],

        "verification" => [
            /**
             * The email verification route to append the id and hash to the end of the URL.
             * Please use the client route for this (SPA thing). Make sure the client route accepts an id and hash param.
             * An expires and signature query param will also be added to the URL.
             */
            "path" => "/email/verify",

            "messages" => [
                "invalid_link" => "Expired or Invalid email verification link",
            ],
        ],
    ],

    "models" => [
        "disabler_column" => "disabled_at",
    ],

    "uploader" => [
        /**
         * The chunk size in bytes
         */
        "chunk_size" => (int) Envf::get("MITD_UPLOADER_CHUNK_SIZE", 1024 * 1024 * 1.75),

        /**
         * The directory to store temporary files in
         */
        "tmp_dir" => "chunks",

        /**
         * The directory to store uploaded files in
         */
        "upload_dir" => "files",

        /**
         * The number of times to retry a chunk upload if it fails
         */
        "chunk_retry" => (int) Envf::get("MITD_UPLOADER_CHUNK_RETRY", 3),

        "cleanup" => [
            /**
             * The cron schedule to run the cleanup
             */
            "cron" => Envf::get("MITD_UPLOADER_CLEANUP_CRON", "0 0 * * *"),

            /**
             * The maximum age of a temporary file to be considered for cleanup
             * in seconds
             * @default 12 hours
             */
            "max_age" => (int) Envf::get("MITD_UPLOADER_CLEANUP_MAX_AGE", 60 * 60 * 12),

            /**
             * Waiting time before a temporary file is considered for cleanup
             * in seconds after being cancelled
             * @default 3 minutes
             */
            "cancel_cleanup_delay" => (int) Envf::get("MITD_UPLOADER_CANCEL_CLEANUP_DELAY", 60 * 3),
        ],
    ],

    "images" => [
        "thumbnail_ext" => "webp",
        "sizes" => [
            "max" => [
                "width" => 2048,
                "height" => 2048,
            ],
            "thumbnails" => [
                // "xs" => [
                //     "width" => 64,
                //     "height" => 64,
                // ],
                "sm" => [
                    "width" => 128,
                    "height" => 128,
                ],
                "md" => [
                    "width" => 256,
                    "height" => 256,
                ],
                "lg" => [
                    "width" => 512,
                    "height" => 512,
                ],
                "xl" => [
                    "width" => 768,
                    "height" => 768,
                ],
            ],
        ],
    ],
];

<?php

use Illuminate\Support\Facades\Schedule;

use App\MITD\FileUpload;

Schedule::command("auth:clear-resets")->everyFifteenMinutes();

Schedule::call(new FileUpload, ["method" => "cleanUp"])->name("File Upload Cleanup")->withoutOverlapping()->cron(config("mitd.uploader.cleanup.cron", "0 0 * * *")); // prettier-ignore

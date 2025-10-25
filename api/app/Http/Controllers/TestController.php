<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\FilesTrait;
use App\Http\Requests\FileUploadRequest;

class TestController extends Controller {
    use FilesTrait;

    public function upload(FileUploadRequest $request) {
        $upload = $this->uploadFileRequest($request);
        if (isset($upload["file"])) {
            return [...$upload["result"], "sha256" => $upload["raw"]["sha256"]];
        }
        return $upload["result"];
    }

    public function logs(Request $request) {
        /**
         * Basic use
         *
         * Other methods are available
         * - info(...)
         * - debug(...)
         * - error(...)
         * - alert(...)
         * - critical(...)
         * - emergency(...)
         * - warning(...)
         */
        trail("Module Name")->info("Action taken here");

        /**
         * You can also pass the old and new data
         *
         * old and new data can ba any data type, it well be converted to json using json_encode internally
         */
        trail("Module Name")->info("Action taken here", newData: $request->all());
        trail("Module Name")->info("Action taken here", newData: $request->all(), oldData: "some old data");

        /**
         * You can also pass the type of the action (ACTION_CREATE, ACTION_UPDATE, ACTION_DELETE, etc...)
         * run trail()::$levels to see all the action types
         *
         * this is for backward compatibility, this will be deprecated and will not be used,
         *
         * Default type is ACTION_UNSPECIFIED
         */
        trail("Module Name")->debug("Action taken here", type: trail()::ACTION_CREATE);

        /**
         * Get levels
         */
        $levels = trail()::$levels;

        /**
         * By default, the logger will get the current authenticated user and will be associated with the log
         * To change this, you can pass the user object as the second parameter to the trail function
         */
        trail("Module Name", $request->user())->info("Action taken here");

        /**
         * The trail function helper will return a new instance of App\MITD\Logger class
         * This is also true for the following methods which allows you to chain multiple logs
         * - info(...), alert(...), critical(...), emergency(...), error(...), warning(...), notice(...), debug(...)
         */
        $logger = trail("Module Name");
        $logger->debug("Debugged something");

        /**
         * Log functions can be chained to generate multiple logs
         * This is useful for logging multiple events in a single request
         */
        trail("Module Name")->info("Action taken here")->alert("Action taken here");

        /**
         * Get the latest log model that was created
         */
        $log = trail("Module Name")->info("Action taken here")->model;

        /**
         * Get all the log models that were created
         */
        $logs = trail("Module Name")->info("Action taken here")->debug("Something happened")->critical("Something happened")->models;
    }
}

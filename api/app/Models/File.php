<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends AppModel {
    protected $fillable = [
        /**
         * The name of the file to be used
         */
        "name",

        /**
         * The original file name of the file
         */
        "old_name",

        /**
         * The generated file name used to store the file in the server
         */
        "file_name",

        /**
         * The mime type of the file
         */
        "mime",

        /**
         * The extension of the file
         */
        "ext",

        /**
         * The size of the file in bytes
         */
        "size",

        /**
         * The folder path to the file
         */
        "path",

        /**
         * The id of the model
         */
        "filable_id",

        /**
         * The model type
         */
        "filable_type",
    ];

    protected $hidden = ["path", "file_name", "old_name"];

    public function filable(): MorphTo {
        return $this->morphTo();
    }
}

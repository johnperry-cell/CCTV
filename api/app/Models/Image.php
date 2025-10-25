<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FS;

class Image extends AppModel {
    protected $fillable = ["file_id", "alt"];

    protected static function boot() {
        parent::boot();

        static::deleted(function ($image) {
            $file = $image->file;
            $main = Storage::disk(config("filesystems.default"))->path(
                join(DIRECTORY_SEPARATOR, [$file->path, $file->file_name . "." . $file->ext])
            );

            if (FS::exists($main)) {
                FS::delete($main);
            }

            if ($file->ext != "gif" && $file->ext != "svg") {
                $thumb_ext = config("mitd.images.thumbnail_ext", "webp");
                $sizes = config("mitd.images.sizes.thumbnails");

                foreach ($sizes as $key => $size) {
                    $thumb = Storage::disk(config("filesystems.default"))->path(
                        join(DIRECTORY_SEPARATOR, [
                            $file->path,
                            "thumbnails",
                            $file->file_name . "_" . $key . "." . $thumb_ext,
                        ])
                    );
                    if (FS::exists($thumb)) {
                        FS::delete($thumb);
                    }
                }
            }

            $image->file()->delete();
        });
    }

    public function file() {
        return $this->belongsTo(File::class);
    }
}

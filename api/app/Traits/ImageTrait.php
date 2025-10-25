<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FS;
use Illuminate\Support\Sleep;

use App\Models\Image;
use App\Models\File;

trait ImageTrait {
    use FilesTrait;
    public function imageDisplay(Request $request, Image $image, ?string $size = null) {
        $file = $image->file;
        $paths = [$file->path];
        $thumb_ext = config("mitd.images.thumbnail_ext", "webp");
        if (!!$size && !in_array($file->ext, ["gif", "svg"])) {
            $paths = array_merge($paths, ["thumbnails", $file->file_name . "_" . $size . "." . $thumb_ext]);
        } else {
            $paths = array_merge($paths, [$file->file_name . "." . $file->ext]);
        }
        $target = Storage::disk(config("filesystems.default"))->path(join(DIRECTORY_SEPARATOR, $paths));

        return response()->file($target, [
            "Content-Type" => $file->mime,
        ]);
    }

    public function resizeImage(Image $image) {
        $file = $image->file;
        $path = Storage::disk(config("filesystems.default"))->path(join(DIRECTORY_SEPARATOR, [$file->path, $file->file_name . "." . $file->ext]));
        $max = config("mitd.images.sizes.max");
        $img = InterventionImage::read($path);

        $img->scaleDown($max["width"], $max["height"])
            ->sharpen()
            ->encodeByExtension("jpg", progressive: true, quality: 100)
            ->save($path . ".tmp");

        FS::delete($path);
        FS::move($path . ".tmp", $path);
        $image->file()->update(["size" => FS::size($path)]);
    }

    public function createThumbnails(Image $image) {
        $file = $image->file;
        $path = Storage::disk(config("filesystems.default"))->path(join(DIRECTORY_SEPARATOR, [$file->path, $file->file_name . "." . $file->ext]));

        $thumb_ext = config("mitd.images.thumbnail_ext", "webp");

        $sizes = config("mitd.images.sizes.thumbnails");
        $destDir = Storage::disk(config("filesystems.default"))->path(join(DIRECTORY_SEPARATOR, [$file->path, "thumbnails"]));

        FS::ensureDirectoryExists($destDir, 0777, true);

        foreach ($sizes as $key => $size) {
            $img = InterventionImage::read($path);
            $dest = join(DIRECTORY_SEPARATOR, [$destDir, $file->file_name . "_" . $key . "." . $thumb_ext]);

            $img->scaleDown($size["width"], $size["height"])->save($dest, quality: 45, progressive: true);
        }
    }
}

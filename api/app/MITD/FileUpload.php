<?php

namespace App\MITD;

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Carbon\Carbon;
use Carbon\CarbonInterval;

use App\MITD\FileUploadException as Exception;

use App\Http\Controllers\UploadController;

class FileUpload {
    const DS = DIRECTORY_SEPARATOR;
    const METANAME = "meta.json";

    public function __invoke(string $method) {
        $self = new static();
        if ($method == "cleanUp") {
            $self->cleanUp();
        }
    }

    public static function prepareUpload(string $filename, string $rename, int $size, string $location = "") {
        $uid = self::createUID();
        self::prepareUploadDir($uid);
        $meta = self::createMetaFile($uid, $filename, $rename, $size, $location);
        return ["result" => ["uid" => $uid, "parts" => $meta["parts"], ...self::getNextPart($uid)]];
    }

    public static function receivePart(UploadedFile $file, string $uid) {
        $meta = self::getMeta($uid);
        $next = self::getNextPart($uid);
        try {
            if ($next["part"] != null) {
                if (!self::isValidSize($file)) {
                    throw new Exception(Exception::CHUNK_SIZE_EXCEEDED);
                }

                $dir = self::getTempDir() . self::DS . $uid;
                $file->move($dir, "_.part_" . $next["part"]);
                array_push($meta["done"], $next["part"]);
                $failed = array_diff($meta["failed"], [$next["part"]]);

                self::updateMeta($uid, "done", $meta["done"]);
                self::updateMeta($uid, "failed", $failed);
                self::updateMeta($uid, "progress", min(count($meta["done"]) / $meta["parts"], 1));

                $result = [
                    "result" => self::getNextPart($uid),
                    "file" => null,
                    "raw" => null,
                ];
                if ($result["result"]["part"] == null) {
                    $result["raw"] = self::mergeChunks($uid);
                }
                return $result;
            }

            throw new Exception(Exception::INVALID_CHUNK);
        } catch (Exception $e) {
            if ($e->getCode() == Exception::INVALID_CHUNK) {
                throw $e;
            }

            if (!self::canRetry($meta, $next)) {
                throw new Exception(Exception::RETRY_EXCEEDED);
            } else {
                array_push($meta["failed"], $next["part"]);
                self::updateMeta($uid, "failed", $meta["failed"]);
            }
            return $next;
        }
    }

    public static function cleanUp() {
        $toClean = self::collectAgedChunks();
        $total = collect([
            "size" => 0,
            "files" => 0,
            "folders" => $toClean
                ->where(function ($chunk) {
                    return !!$chunk["uid"];
                })
                ->count(),
        ]);
        $toClean->each(function ($chunk) use ($total) {
            $size = self::folderSize($chunk["dir"]);
            $total["size"] += $size["size"];
            $total["files"] += $size["files"];
            $total["folders"] += $size["folders"];
            File::deleteDirectory($chunk["dir"]);
        });
    }

    public static function collectAgedChunks() {
        $tmp = self::getTempDir();
        $paths = collect([]);
        $aged = collect([]);
        $forced = collect([]);
        collect(File::directories($tmp))->each(function ($dir) use ($paths, $forced) {
            $path = join(self::DS, [$dir, self::METANAME]);
            if (File::exists($path)) {
                $paths->push([
                    "meta" => $path,
                    "dir" => $dir,
                ]);
            } else {
                $forced->push($dir);
            }
        });
        $now = now();

        $paths->each(function ($path) use ($now, $aged) {
            $meta = self::readMeta($path["meta"]);
            $age = Carbon::createFromTimestamp($meta["updated_at"])->diffInSeconds($now);
            if ($age > config("mitd.uploader.cleanup.max_age")) {
                $aged->push([
                    "uid" => $meta["uid"],
                    "dir" => $path["dir"],
                    "age" => CarbonInterval::seconds($age)->cascade()->forHumans(),
                ]);
            }
        });

        $forced->each(function ($dir) use ($aged) {
            $aged->push([
                "uid" => "",
                "dir" => $dir,
                "age" => 0,
            ]);
        });

        return $aged;
    }

    public static function pauseUpload(string $uid) {
        $meta = self::getMeta($uid);
        if (!$meta["paused"]) {
            $meta["paused"] = true;
            self::updateMeta($uid, "paused", true);
        }
        return ["result" => ["uid" => $uid, "parts" => $meta["parts"], ...self::getNextPart($uid)]];
    }

    public static function resumeUpload(string $uid) {
        $meta = self::getMeta($uid);
        if ($meta["paused"]) {
            $meta["paused"] = false;
            self::updateMeta($uid, "paused", false);
        }
        return ["result" => ["uid" => $uid, "parts" => $meta["parts"], ...self::getNextPart($uid)]];
    }

    public static function cancelUpload(string $uid) {
        $meta = self::getMeta($uid);
        $meta["cancelled"] = true;
        self::updateMeta($uid, "cancelled", true);
        dispatch(function () use ($uid) {
            File::deleteDirectory(self::getMetaDir($uid));
        })
            ->afterResponse()
            ->delay(CarbonInterval::seconds(config("mitd.uploader.cleanup.cancel_cleanup_delay")));

        return true;
    }

    public static function getNextPart(string $uid) {
        $meta = self::getMeta($uid);
        $completed = $meta["done"];
        $failed = $meta["failed"];
        sort($completed);
        sort($failed);

        $last = [
            "completed" => $completed[count($completed) - 1] ?? 0,
            "failed" => $failed[count($failed) - 1] ?? 0,
            "part" => 0,
        ];

        $last["part"] = max($last["completed"], $last["failed"]);
        $lastFailed = $last["failed"] > $last["completed"];

        $result = [
            "part" => $last["part"] + 1,
            "length" => $meta["chunk_size"],
            "progress" => $meta["progress"],
            "previous" =>
                $last["part"] == 0
                    ? null
                    : [
                        "uploaded" => !$lastFailed,
                        "part" => $last["part"],
                    ],
            "status" => $meta["paused"] ? "paused" : "uploading",
        ];

        if (count($meta["done"]) >= $meta["parts"]) {
            $result["part"] = null;
            $result["length"] = 0;
            $result["status"] = "complete";
        }

        if ($last["part"] == $meta["parts"] && count($failed) > 0) {
            $result["part"] = $failed[0];
            $result["length"] = $meta["chunk_size"];
        }

        return $result;
    }

    public static function routes() {
        Route::prefix("upload")->group(function () {
            Route::put("pause", [UploadController::class, "pauseUploads"])->name("upload.pause");
            Route::put("resume", [UploadController::class, "resumeUploads"])->name("upload.resume");
            Route::put("cancel", [UploadController::class, "cancelUploads"])->name("upload.cancel");
        });
    }

    private static function folderSize($dir) {
        $result = [
            "size" => 0,
            "files" => 0,
            "folders" => 0,
        ];
        $files = File::allFiles($dir);
        $dirs = File::directories($dir);

        foreach ($files as $file) {
            $result["size"] += File::size($file);
            $result["files"] += 1;
        }
        foreach ($dirs as $dir) {
            $tmp = self::folderSize($dir);
            $result["size"] += $tmp["size"];
            $result["files"] += $tmp["files"];
            $result["folders"] += $tmp["folders"] + 1;
        }
        return $result;
    }

    private static function formatSize($bytes) {
        $units = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
        $i = 0;
        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            ++$i;
        }
        return round($bytes, 2) . " " . $units[$i];
    }

    private static function isValidSize(UploadedFile $file): bool {
        return $file->getSize() <= config("mitd.uploader.chunk_size", 1024 * 1024 * 10);
    }

    private static function canRetry($meta, $next): bool {
        return !(
            count($meta["failed"]) > 0 &&
            in_array($next["part"], $meta["failed"]) &&
            array_count_values($meta["failed"])[$next["part"]] >= config("mitd.uploader.chunk_retry")
        );
    }

    private static function createUID(): string {
        return join("_", [uniqid(), time()]);
    }

    private static function getTempDir(): string {
        return Storage::disk(config("filesystems.default"))->path(config("mitd.uploader.tmp_dir", "chunks"));
    }

    private static function getUploadDir($location = ""): string {
        $folders = [config("mitd.uploader.upload_dir", "files"), $location];
        return Storage::disk(config("filesystems.default"))->path(join(self::DS, $folders));
    }

    private static function getMetaPath(string $uid): string {
        return join(self::DS, [self::getMetaDir($uid), self::METANAME]);
    }

    private static function getMetaDir(string $uid): string {
        return join(self::DS, [self::getTempDir(), $uid]);
    }

    private static function prepareUploadDir(string $uid): string {
        $path = self::getTempDir() . self::DS . $uid;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    private static function mergeChunks(string $uid) {
        $meta = self::getMeta($uid);
        $destination = self::getUploadDir($meta["location"]);

        $ext = pathinfo($meta["name"], PATHINFO_EXTENSION);
        $dir = join(self::DS, [self::getTempDir(), $uid]);
        $tmpFile = join(self::DS, [$destination, $uid . "." . $ext]);

        File::ensureDirectoryExists($destination, 0777, true);

        foreach ($meta["done"] as $part) {
            $chunk = join(self::DS, [$dir, "_.part_" . $part]);
            File::append($tmpFile, File::get($chunk));
        }
        File::deleteDirectory($dir);

        $oldname = basename($meta["rename"]) == basename($meta["name"]) ? null : $meta["name"];

        $result = [
            "name" => basename($meta["rename"]),
            "old_name" => $oldname,
            "file_name" => $uid,
            "path" => self::trimSlash(join(self::DS, [config("mitd.uploader.upload_dir", "files"), $meta["location"]])),
            "mime" => File::mimeType($tmpFile),
            // "ext" => File::guessExtension($tmpFile),
            "ext" => $ext,
            "size" => File::size($tmpFile),
            "md5" => File::hash($tmpFile, "md5"),
            "sha1" => File::hash($tmpFile, "sha1"),
            "sha256" => File::hash($tmpFile, "sha256"),
        ];

        return $result;
    }

    private static function createMetaFile(string $uid, string $filename, string $rename, int $size, string $location = "") {
        $now = time();
        $path = self::getMetaPath($uid);
        $chunkSize = (int) config("mitd.uploader.chunk_size");
        $data = [
            "uid" => $uid,
            "name" => self::sanitizeName($filename),
            "rename" => self::sanitizeName($rename),
            "size" => $size,
            "chunk_size" => $chunkSize,
            "parts" => self::computeParts($size, $chunkSize),
            "location" => self::trimSlash($location),
            "done" => [],
            "failed" => [],
            "paused" => false,
            "cancelled" => false,
            "progress" => 0,
            "created_at" => $now,
            "updated_at" => $now,
        ];
        File::put($path, json_encode($data));
        return $data;
    }

    private static function getMeta(string $uid) {
        $path = self::getMetaPath($uid);
        return self::readMeta($path);
    }

    private static function readMeta(string $path) {
        if (!File::exists($path)) {
            throw new Exception(Exception::INVALID_UID);
        }
        return json_decode(File::get($path), true);
    }

    private static function updateMeta(string $uid, $key, $value) {
        $meta = self::getMeta($uid);
        if (isset($meta[$key])) {
            $meta[$key] = $value;
            $meta["updated_at"] = time();
            $path = self::getMetaPath($uid);
            File::put($path, json_encode($meta));
        }
    }

    private static function computeParts(int $size, int $chunkSize): int {
        return (int) ceil($size / $chunkSize);
    }

    private static function trimSlash(string $path): string {
        $path = str_replace(["/", "\\"], self::DS, $path);
        $parts = array_filter(explode(self::DS, $path), "strlen");
        $absolutes = [];
        foreach ($parts as $part) {
            if ("." == $part) {
                continue;
            }
            if (".." == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(self::DS, $absolutes);
    }

    /**
     * @param $fname string The name of the file
     * @param $replacement string The character to replace illegal characters
     * @return string
     */
    private static function sanitizeName(string $fname, string $replacement = " ") {
        $tmp = collect(preg_split("/\r\n|\n|\r/", $fname));
        $tmp = $tmp->map(function ($item, $key) {
            return trim($item);
        });
        $tmp = array_filter($tmp->toArray());

        $result = join(" ", $tmp);

        $result = preg_replace("/[\r\n]/", " ", $fname);

        $result = preg_replace('/[<>:"\/\\\|?*\x00-\x1F]/', $replacement, $result);

        $result = preg_replace('/\.$/', $replacement, $result);

        if (strlen($result) <= 0) {
            $result = "_" . $result;
        }

        if (preg_match('/^(CON|PRN|AUX|NUL|COM1|COM2|COM3|COM4|COM5|COM6|COM7|COM8|COM9|LPT1|LPT2|LPT3|LPT4|LPT5|LPT6|LPT7|LPT8|LPT9)(\..+)?$/', $result)) {
            $result = "_" . $result;
        }

        return $result;
    }
}

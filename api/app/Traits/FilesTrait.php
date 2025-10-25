<?php

namespace App\Traits;

use App\Http\Requests\FileUploadRequest;
use Illuminate\Support\Facades\Storage;

use App\MITD\FileUpload;
use App\Models\File;

trait FilesTrait {
    protected function uploadFileRequest(FileUploadRequest $request, $location = "", $fileRule = "required|file") {
        $validated = $request->validated();
        $uid = $validated["uid"] ?? null;
        if (!$uid) {
            return FileUpload::prepareUpload($validated["name"], $validated["rename"], $validated["size"], $location);
        } else {
            $next = FileUpload::getNextPart($uid);
            if ($next["part"] == 1) {
                $request->validate(["file" => $fileRule]);
            }
            $received = FileUpload::receivePart($request->file("file"), $uid);
            if (isset($received["raw"])) {
                $received["file"] = File::create($received["raw"]);
            }
            return $received;
        }
    }

    protected function previewFile(File $file) {
        $fullPath = $this->getFilePath($file);
        $headers = ["Content-Type" => $file->mime];
        return response()->file($fullPath, $headers);
    }

    protected function downloadFile(File $file) {
        $fullPath = $this->getFilePath($file);
        $headers = [
            "Content-Type" => $file->mime,
            "Content-Length" => $file->size,
            "Content-disposition" => 'attachment; filename=" ' . $file->name . '"',
        ];

        return response()->download($fullPath, $file->name, $headers);
    }

    private function getFilePath(File $file) {
        $filePath = join(DIRECTORY_SEPARATOR, [$file->path, $file->file_name . "." . $file->ext]);
        if (!Storage::disk()->exists($filePath)) {
            return abort(422, "File not Found!");
        }
        return Storage::disk()->path($filePath);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\MITD\FileUploadException as Exception;
use App\MITD\FileUpload;

class UploadController extends Controller {
    public function pauseUploads(Request $request) {
        $uids = collect($request->input("uids", []));
        $uids->each(function ($uid) {
            try {
                FileUpload::pauseUpload($uid);
            } catch (\Exception $e) {
                if ($e->getCode() != Exception::INVALID_UID) {
                    throw $e;
                }
            }
        });
        return response(["message" => "Uploads paused"]);
    }

    public function resumeUploads(Request $request) {
        $uids = collect($request->input("uids", []));
        $uids->map(function ($uid) {
            try {
                FileUpload::resumeUpload($uid);
            } catch (\Exception $e) {
                if ($e->getCode() != Exception::INVALID_UID) {
                    throw $e;
                }
            }
        });
        return response(["message" => "Uploads resumed"]);
    }

    public function cancelUploads(Request $request) {
        $uids = collect($request->input("uids", []));
        $uids->each(function ($uid) {
            try {
                FileUpload::cancelUpload($uid);
            } catch (\Exception $e) {
                if ($e->getCode() != Exception::INVALID_UID) {
                    throw $e;
                }
            }
        });
        return response(["message" => "Uploads cancelled"]);
    }
}

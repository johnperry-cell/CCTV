<?php

namespace App\MITD;

class FileUploadException extends \Exception {
    const INVALID_CHUNK = 1700;
    const INVALID_UID = 1701;
    const CHUNK_SIZE_EXCEEDED = 1702;
    const RETRY_EXCEEDED = 1703;

    public static $messages = [
        1700 => "Invalid chunk!",
        1701 => "Invalid UID!",
        1702 => "Chunk size exceeded allowed limit!",
        1703 => "Too many failed attempts!",
    ];

    public function __construct($code = 0, $message = "", $previous = null) {
        if (array_key_exists($code, self::$messages)) {
            $message = "UPL_ERR[" . $code . "]: " . self::$messages[$code];
        } else {
            if ($message == "") {
                $message = "Unknown error!";
            }
        }
        parent::__construct($message, $code, $previous);
    }
}

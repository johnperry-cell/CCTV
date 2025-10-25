<?php

namespace App\MITD;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Log as LogModel;
use App\Models\User;

class Logger {
    const EMERGENCY = 1;
    const ALERT = 2;
    const CRITICAL = 3;
    const ERROR = 4;
    const WARNING = 5;
    const NOTICE = 6;
    const INFO = 7;
    const DEBUG = 8;

    const ACTION_UNSPECIFIED = 0;
    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;
    const ACTION_LOGIN = 4;
    const ACTION_LOGOUT = 5;
    const ACTION_DISABLE = 6;
    const ACTION_ENABLE = 7;
    const ACTION_SEND_EMAIL = 8;

    const ACTION_SAVING = 9;
    const ACTION_SAVED = 10;
    const ACTION_RETRIEVED = 11;
    const ACTION_RESTORED = 12;
    const ACTION_RESTORING = 13;
    const ACTION_REPLICATING = 14;
    const ACTION_CREATING = 15;
    const ACTION_UPDATING = 16;
    const ACTION_DELETING = 17;

    protected $LOG_LEVEL = self::INFO;
    protected $LOG_TYPE = self::ACTION_UNSPECIFIED;
    protected $MODULE_STACK_LEVEL = 2;

    public $models = [];
    public $model = null;
    public $module = null;
    public $user = null;

    public static $levels = [
        self::EMERGENCY => "emergency",
        self::ALERT => "alert",
        self::CRITICAL => "critical",
        self::ERROR => "error",
        self::WARNING => "warning",
        self::NOTICE => "notice",
        self::INFO => "info",
        self::DEBUG => "debug",
    ];

    public static $types = [
        self::ACTION_UNSPECIFIED => "Unspecified",
        self::ACTION_CREATE => "Create",
        self::ACTION_UPDATE => "Update",
        self::ACTION_DELETE => "Delete",
        self::ACTION_LOGIN => "Login",
        self::ACTION_LOGOUT => "Logout",
        self::ACTION_DISABLE => "Disable",
        self::ACTION_ENABLE => "Enable",
        self::ACTION_SEND_EMAIL => "Send Email",

        self::ACTION_SAVING => "Saving",
        self::ACTION_SAVED => "Saved",
        self::ACTION_RETRIEVED => "Retrieved",
        self::ACTION_RESTORED => "Restored",
        self::ACTION_RESTORING => "Restoring",
        self::ACTION_REPLICATING => "Replicating",
        self::ACTION_CREATING => "Creating",
        self::ACTION_UPDATING => "Updating",
        self::ACTION_DELETING => "Deleting",
    ];

    /**
     * Create a new class instance.
     */
    public function __construct(string $module = null, User $user = null) {
        $this->module = $module;
        $this->user = $user;
    }

    private function recordActivity(
        string $action,
        string $module,
        int $type,
        mixed $oldData = null,
        mixed $newData = null,
        int $level = 7,
        User $user = null,
        string $actor = null,
        Carbon $timestamp = null
    ): LogModel {
        $user = $user ?? ($this->user ?? Auth::user());
        $name = $actor ?? ($user?->profile?->fullName() ?? ($user->username ?? null));

        $this->model = LogModel::create([
            "action" => $action,
            "module" => $module,
            "type" => $type,
            "old_data" => trim(json_encode($oldData)),
            "new_data" => trim(json_encode($newData)),
            "level" => $level,
            "user_id" => $user?->id ?? null,
            "actor" => $name,
        ]);
        if ($timestamp) {
            $this->model->created_at = $timestamp;
            $this->model->save();
        }
        $this->models[] = $this->model;
        return $this->model;
    }

    public function randomTest($length = 100, $sentence = null, $level = null) {
        $users = User::limit(5)->get();

        for ($i = 1; $i <= $length; $i++) {
            $action = fake()->sentence($sentence ?? fake()->numberBetween(15, 35));
            $old = fake()->boolean(0.5) ? null : $this->fakeJson();
            $new = fake()->boolean(0.5) ? null : $this->fakeJson();
            $this->recordActivity(
                $action,
                $this->getModule(),
                self::ACTION_UNSPECIFIED,
                $old,
                $new,
                $level ?? fake()->numberBetween(1, 7),
                $users->random(),
                fake()->name(),
                Carbon::parse(fake()->dateTimeBetween("-5 year", "now"))
            );
        }
        return $this;
    }

    public function emergency(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::EMERGENCY);
        return $this;
    }

    public function alert(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::ALERT);
        return $this;
    }

    public function critical(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::CRITICAL);
        return $this;
    }

    public function error(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::ERROR);
        return $this;
    }

    public function warning(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::WARNING);
        return $this;
    }

    public function notice(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::NOTICE);
        return $this;
    }

    public function info(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::INFO);
        return $this;
    }

    public function debug(string $action, int $type = self::ACTION_UNSPECIFIED, mixed $oldData = null, mixed $newData = null) {
        $this->recordActivity($action, $this->getModule(), $type, $oldData, $newData, self::DEBUG);
        return $this;
    }

    private function getModule() {
        if ($this->module) {
            return $this->module;
        }
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7)[$this->MODULE_STACK_LEVEL];
        $mod = isset($stack["class"]) ? $stack["class"] . ":" : $stack["file"] . ":" . $stack["line"] . " => ";
        return $mod . $stack["function"];
    }

    private function getUser(User $user = null) {
        if ($user) {
            return $user;
        }

        return $this->user ?? Auth::user();
    }

    private function fakeJson($length = 5) {
        $data = [];
        for ($i = 0; $i < $length; $i++) {
            $data[] = $this->fakeJsonData();
        }
        return $data;
    }

    private function fakeJsonData() {
        return [
            "id" => fake()->uuid(),
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "email" => fake()->safeEmail(),
        ];
    }
}
